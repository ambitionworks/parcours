<?php

namespace App\Jobs;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use adriangibbons\phpFITFileAnalysis as FitFile;
use Illuminate\Support\Facades\Cache;
use MStaack\LaravelPostgis\Geometries\Point;
use MStaack\LaravelPostgis\Geometries\MultiPoint;

class ProcessActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Activity model being processed.
     *
     * @var Activity $activity
     */
    public Activity $activity;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fit = new FitFile(Storage::path($this->activity->upload->file_path));
        $activity = $this->activity;

        $activity->performed_at = Carbon::createFromTimestamp($fit->data_mesgs['session']['start_time']);
        $activity->name = $activity->name ?? (
            $fit->enumData('sub_sport', $fit->data_mesgs['session']['sub_sport']) !== 'Generic'
                ? $fit->enumData('sub_sport', $fit->data_mesgs['session']['sub_sport'])
                : $fit->sport()
        );
        $activity->duration = $fit->data_mesgs['session']['total_elapsed_time'];
        $activity->active_duration = $fit->data_mesgs['session']['total_timer_time'];
        $activity->distance = $fit->data_mesgs['session']['total_distance'];
        $activity->stationary = !isset($fit->data_mesgs['record']['position_lat']) || !is_array($fit->data_mesgs['record']['position_lat']) || count($fit->data_mesgs['record']['position_lat']) === 0;
        $activity->has_laps = is_array($fit->data_mesgs['lap']['start_time']);
        $activity->ascent = $fit->data_mesgs['session']['total_ascent'] ?? null;
        $activity->descent = $fit->data_mesgs['session']['total_descent'] ?? null;
        $activity->avg_power = $fit->data_mesgs['session']['avg_power'] ?? null;
        $activity->avg_hr = $fit->data_mesgs['session']['avg_heart_rate'] ?? null;
        $activity->avg_speed = $fit->data_mesgs['session']['avg_speed'] ?? null;
        $activity->avg_cadence = $fit->data_mesgs['session']['avg_cadence'] ?? null;
        $activity->max_power = $fit->data_mesgs['session']['max_power'] ?? null;
        $activity->max_hr = $fit->data_mesgs['session']['max_heart_rate'] ?? null;
        $activity->max_speed = $fit->data_mesgs['session']['max_speed'] ?? null;
        $activity->max_cadence = $fit->data_mesgs['session']['max_cadence'] ?? null;

        if ($activity->user->metric_profile) {
            $activity->ftp = $activity->user->metric_profile->ftp;
            $activity->hr_resting = $activity->user->metric_profile->hr_resting;
            $activity->hr_max = $activity->user->metric_profile->hr_max;
            $activity->hr_lt = $activity->user->metric_profile->hr_lt;
        }

        if (isset($activity->ftp) && !empty($fit->data_mesgs['record']['power'])) {
            $metrics = $fit->powerMetrics($activity->ftp);
            $activity->np = $metrics['Normalised Power'];
            $activity->tss = $metrics['Training Stress Score'];
            $activity->if = $metrics['Intensity Factor'];
        }

        if (isset($fit->data_mesgs['session']['start_position_lat'], $fit->data_mesgs['session']['start_position_long'])) {
            if (empty($activity->tz_offset) && isset($fit->data_mesgs['session']['start_position_lat'])) {
                $tz = file_get_contents(
                    sprintf('https://maps.googleapis.com/maps/api/timezone/json?location=%s,%s&timestamp=%d&key=%s',
                        $fit->data_mesgs['session']['start_position_lat'],
                        $fit->data_mesgs['session']['start_position_long'],
                        $fit->data_mesgs['session']['start_time'],
                        config('parcours.google_tz_key')
                    )
                );
                $tz = json_decode($tz);
                $activity->tz_offset = ($tz->rawOffset ?? 0) + ($tz->dstOffset ?? 0);
            }

            $route = [];
            foreach ($fit->data_mesgs['record']['position_lat'] as $timestamp => $lat) {
                if (isset($lat, $fit->data_mesgs['record']['position_long'][$timestamp])) {
                    $route[] = new Point($lat, $fit->data_mesgs['record']['position_long'][$timestamp]);
                }
            }
            $activity->route = new MultiPoint($route);

            $segment_ids = [];
            $timestamp_index = array_flip(array_keys($fit->data_mesgs['record']['position_lat']));
            $activity->segments()->detach();
            foreach ($activity->getMatchingSegments() as $segment_id => $segment) {
                if (!isset($segment->performance)) {
                    continue;
                } else {
                    $segment_ids[] = $segment_id;
                }
                foreach ($segment->performance as $performance) {
                    $pivot = [
                        'start_time' => $performance['start'],
                        'end_time' => $performance['end'],
                    ];

                    foreach(['heart_rate' => 'avg_hr', 'power' => 'avg_power', 'speed' => 'avg_speed', 'cadence' => 'avg_cadence'] as $fitKey => $pivotKey) {
                        if (isset($fit->data_mesgs['record'][$fitKey])) {
                            $slice = array_slice($fit->data_mesgs['record'][$fitKey], $timestamp_index[$performance['start']], ($performance['end'] - $performance['start']));
                            $pivot[$pivotKey] = array_sum($slice) / count($slice);
                        }
                    }

                    $activity->segments()->attach([
                        $segment_id => $pivot,
                    ]);
                }
            }
        } else {
            if (empty($activity->tz_offset)) {
                $user_tz = $activity->user->default_timezone ?? 'UTC';
                $date_time_zone = new \DateTimeZone($user_tz);
                $date_time = new \DateTime("now", $date_time_zone);
                $activity->tz_offset = $date_time_zone->getOffset($date_time);
            }
        }

        $activity->processed_at = new Carbon();
        $activity->save();

        $cacheTags = [
            sprintf('activity:%d', $activity->id),
        ];
        if (!empty($segment_ids)) {
            foreach ($segment_ids as $id) {
                $cacheTags[] = sprintf('user:%d:segment:%d', $activity->user->id, $id);
            }
        }

        Cache::tags($cacheTags)->flush();

        AccumulateStats::dispatchIf(!$activity->stats_accounted, $activity, AccumulateStats::OP_ADD);
    }
}
