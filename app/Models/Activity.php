<?php

namespace App\Models;

use App\Jobs\ProcessActivity;
use App\Jobs\AccumulateStats;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;
use adriangibbons\phpFITFileAnalysis as FitFile;
use Multicaret\Acquaintances\Traits\CanBeLiked;

class Activity extends Model
{
    use PostgisTrait, CanBeLiked;

    /**
     * The attributes that are managed by the PostgisTrait trait.
     *
     * @var array
     */
    protected $postgisFields = [
        'route',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'performed_at' => 'datetime',
    ];

    public static $statable = ['tss', 'distance', 'active_duration', 'ascent'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($activity) {
            $activity->user()->associate(Auth::user());
        });

        static::created(function ($activity) {
            ProcessActivity::dispatch($activity);
        });

        static::deleting(function ($activity) {
            AccumulateStats::dispatchNow($activity, AccumulateStats::OP_SUBTRACT);
        });

        static::deleted(function ($activity) {
            $activity->upload->delete();
            $activity->segments()->detach();
        });
    }

    /**
     * User this Activity belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ActivityUpload responsible for creating this Activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function upload(): BelongsTo
    {
        return $this->belongsTo(ActivityUpload::class, 'activity_upload_id');
    }

    /**
     * Comments attached to this Activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Segments matched to this activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function segments(): BelongsToMany
    {
        return $this->belongsToMany(Segment::class)
            ->using(ActivitySegment::class)
            ->withPivot(
                'start_time',
                'end_time',
                'avg_speed',
                'avg_power',
                'avg_hr',
                'avg_cadence',
            );
    }

    public function getPerformedAtTzAttribute()
    {
        return $this->performed_at->setTimezone(\Carbon\CarbonTimezone::createFromMinuteOffset($this->tz_offset / 60));
    }

    public function getFitFileAttribute()
    {
        return new FitFile(Storage::path($this->upload->file_path));
    }

    /**
     * Get (optionally time-sliced) GeoJson struct for an activity.
     *
     * @param int $start
     * @param int $end
     * @return array
     */
    public function getGeoJson(int $start = null, int $end = null, $gaps = true): array
    {
        if (!isset($start, $end)) {
            $cacheKey = sprintf('activity:%d:geojson', $this->id);
        } else if (isset($start, $end)) {
            $cacheKey = sprintf('activity:%d:geojson:%d:%d', $this->id, $start, $end);
        } else {
            throw new \ErrorException('Missing start and end times');
        }

        $cacheKey .= ':gaps' . ($gaps ? '-yes' : '-no');

        return Cache::tags(sprintf('activity:%d', $this->id))->remember($cacheKey, 3600, function () use ($start, $end, $gaps) {
            $output = ['features' => [], 'type' => 'FeatureCollection'];
            $fit = new FitFile(Storage::path($this->upload->file_path));

            $alts = $fit->data_mesgs['record']['altitude'] ?? [];
            $lats = $fit->data_mesgs['record']['position_lat'] ?? [];
            $lons = $fit->data_mesgs['record']['position_long'] ?? [];
            $dist = $fit->data_mesgs['record']['distance'] ?? [];

            if (!$start) {
                $start = count($lats) ? array_key_first($lats) : null;
                $end = count($lats) ? array_key_last($lats) : null;
                $distance_offset = 0;
            } else {
                $distance_offset = $dist[$start];
            }

            if (count($lats) && count($lons) && $start && $end) {
                $last_lat_lon = [];
                $lat_lon = [];
                $altitude = [];
                $distance = [];
                $timestamps = [];

                for ($timestamp = $start; $timestamp <= $end; $timestamp++) {
                    if ($gaps) {
                        if (isset($lats[$timestamp], $lons[$timestamp])) {
                            $lat_lon[] = $last_lat_lon = [$lons[$timestamp], $lats[$timestamp]];
                        } else {
                            $lat_lon[] = $last_lat_lon;
                        }
                        $altitude[] = [$timestamp => $alts[$timestamp] ?? null];
                        $distance[] = [$timestamp => $dist[$timestamp] ?? null];
                        $timestamps[] = $timestamp;
                    } else {
                        if (isset($lats[$timestamp], $lons[$timestamp], $alts[$timestamp], $dist[$timestamp])) {
                            $lat_lon[] = [$lons[$timestamp], $lats[$timestamp]];
                            $altitude[] = $alts[$timestamp];
                            $distance[] = $dist[$timestamp] - $distance_offset;
                            $timestamps[] = $timestamp;
                        }
                    }
                }

                $output['features'][] = [
                    'properties' => ['coordTimes' => $timestamps, 'altitude' => $altitude, 'distance' => $distance],
                    'geometry' => ['coordinates' => $lat_lon, 'type' => 'LineString'],
                    'type' => 'Feature'
                ];
            } else {
                $output['features'][] = [
                    'properties' => ['coordTimes' => $fit->data_mesgs['record']['timestamp']],
                    'geometry' => ['coordinates' => [], 'type' => 'LineString'],
                    'type' => 'Feature'
                ];
            }
            return $output;
        });
    }

    /**
     * Undocumented function
     *
     * @param boolean $force
     * @return array
     */
    public function getSegmentAchievements($force = false): array
    {
        $cacheKey = sprintf('activity:%d:achievements', $this->id);
        $cacheTags = [sprintf('activity:%d', $this->id)];

        $callback = function () use (&$cacheTags) {
            $results = [0, 0, 0];
            $records = $this->getSegmentRecords(true);
            foreach ($this->segments as $segment) {
                $cacheTags[] = sprintf('user:%d:segment:%d', Auth::id(), $segment->id);
                $segment_elapsed = $segment->pivot->end_time - $segment->pivot->start_time;
                for ($i = 0; $i < 3; $i++) {
                    if (isset($records[$segment->id][$i])) {
                        $results[$i] += $records[$segment->id][$i]->elapsed === $segment_elapsed ? 1 : 0;
                    }
                }
            }
            return $results;
        };

        if ($force) {
            Cache::forget($cacheKey);
        }
        // Since $callback mutates $cacheTags, this is done a little different
        if (!Cache::has($cacheKey)) {
            $results = $callback();
            Cache::tags($cacheTags)->put($cacheKey, $results, 3600);
        } else {
            $results = Cache::get($cacheKey);
        }

        return $results;
    }

    /**
     * Get the user's top 3 performances for the segments matched to this
     * activity.
     *
     * @param boolean $force
     * @return \Illuminate\Support\Collection
     */
    public function getSegmentRecords($force = false): Collection
    {
        $cacheKey = sprintf('activity:%d:records', $this->id);
        if ($force) {
            Cache::forget($cacheKey);
        }
        return Cache::tags([sprintf('activity:%d', $this->id)])->remember($cacheKey, 3600, function () {
            $segment_ids = $this->segments->pluck('id')->unique()->toArray();
            if (!count($segment_ids)) {
                return collect([]);
            }
            $records = DB::select(
                DB::raw(
                    'SELECT activity_id, segment_id, (end_time - start_time) AS elapsed FROM activity_segment s
                    LEFT JOIN activities a ON s.activity_id = a.id
                    WHERE a.user_id = ? AND segment_id IN (' . implode(',', $segment_ids) . ')
                    ORDER BY elapsed ASC LIMIT 3'
                ),
                [Auth::id()]
            );
            return collect($records)->sortBy('elapsed')->mapToGroups(function ($item) {
                return [$item->segment_id => $item];
            })->map(function ($items) {
                return $items->slice(0, 3);
            });
        });
    }

    /**
     * Determine what segments the activity overlaps.
     *
     * @param integer $approximate_distance
     * @param float $confidence
     * @return \Illuminate\Support\Collection
     */
    public function getMatchingSegments($approximate_distance = 12, $confidence = 0.9): Collection
    {
        // Collect Segment IDs with a start and end point within the activity
        // route bounding box.
        // @todo Assuming this is simple bbox (SW-NE), could this be optimized
        //       by drawing a poly over the route?
        $segment_ids = collect(DB::select(
            DB::raw(
                'SELECT id FROM segments WHERE start_point &&
                (SELECT route FROM activities WHERE id = ?) AND end_point && (SELECT route FROM activities WHERE id = ?)'
            ),
            [$this->id, $this->id])
        )->pluck('id')->toArray();

        if (!count($segment_ids)) {
            return collect([]);
        }

        // Check to see if ($confidence * 100)% of the segment route is within
        // $approximate_distance meters of the activity's route.
        // https://gis.stackexchange.com/a/5672
        $segments = collect(DB::select(
            DB::raw(
                'SELECT s.id, s.name, COUNT(a.id)/num_of_points::float AS confidence FROM
                (SELECT ST_NPoints(geometry(route)) AS num_of_points,
                (ST_Dumppoints(geometry(route))).geom AS p, id, name FROM segments) s
                INNER JOIN
                activities a
                ON ST_DWithin(s.p, a.route, ' . $approximate_distance . ') WHERE a.id = ? AND s.id IN (' . implode(', ', $segment_ids) . ') GROUP BY s.id, s.name, num_of_points
                HAVING COUNT(a.id)/num_of_points::float > ' . $confidence
            ),
            [
                $this->id,
            ]
        ))->keyBy('id');

        if (!$segments->count()) {
            Log::warning(sprintf('Activity #%d: No segments', $this->id));
            return collect([]);
        } else {
            Log::info(sprintf('Activity #%d: Potential segments', $this->id), $segments->toArray());
        }

        // Okay, now we know what segments were in the ride! Now we need to
        // pull the data from the route. Keep in mind that a segment may appear
        // in a activity route more than once because of weirdos doing hill
        // repeats or something.
        // This essentially reverses the above query to get the recorded lon
        // and lat from the activity that are approximately close to a segment
        // route, as well as how close the point is to a segment's start point
        // and end point.
        $fit = new FitFile(Storage::path($this->upload->file_path));
        $lats = $fit->data_mesgs['record']['position_lat'];
        $lons = $fit->data_mesgs['record']['position_long'];

        // This will convert a point to a timestamp from the activity records.
        $matchPointToTimestamp = function ($point) use ($lats, $lons) {
            $matchedTimestamp = false;
            $keyedTimeStamps = array_keys($lats);
            $matchedTimestampIndexes = array_keys(array_values($lats), $point->lat);
            foreach ($matchedTimestampIndexes as $timestampIndex) {
                $potentialTimestamp = $keyedTimeStamps[$timestampIndex];
                if ($lons[$potentialTimestamp] == $point->lon) {
                    $matchedTimestamp = $potentialTimestamp;
                    break;
                }
            }
            return $matchedTimestamp;
        };

        // Given a point object with lat and lon properties, find the
        // corresponding timestamp from the activity data.
        $points = DB::select(
            DB::raw(
                'SELECT ST_X(a.p) AS lon, ST_Y(a.p) AS lat, ST_DISTANCE(a.p, s.start_point) AS start_distance, ST_DISTANCE(a.p, s.end_point) AS end_distance, s.id FROM
                (SELECT (ST_Dumppoints(geometry(route))).geom AS p, id FROM activities) a
                INNER JOIN
                segments s
                ON ST_DWithin(s.route, a.p, ' . $approximate_distance . ') WHERE a.id = ? AND s.id IN (' . implode(', ', $segments->pluck('id')->toArray()) . ')'
            ),
            [$this->id]
        );

        // Sort the points by how close they are to a segment start.
        $sorted = collect($points)->sortBy('start_distance')->toArray();
        $tracked = [];
        foreach ($sorted as $routeFirstIdx => $routeFirstPoint) {
            if (isset($tracked[$routeFirstPoint->id][$routeFirstIdx])) {
                continue;
            }
            if ($routeFirstPoint->start_distance >= $approximate_distance) {
                break;
            }

            if (($startingTimestamp = $matchPointToTimestamp($routeFirstPoint))) {
                for ($i = $routeFirstIdx; $i < count($sorted); $i++) {
                    if (!isset($sorted[$i])) {
                        break;
                    }
                    if ($sorted[$i]->end_distance <= $approximate_distance) {
                        // Take a window of the next few points to see which is the closest.
                        $closestEndIndex = $i;
                        $closestEndDistance = $sorted[$i]->end_distance;
                        $closestEndTimestamp = $matchPointToTimestamp($sorted[$closestEndIndex]);
                        for ($j = $i; $j < $i + 5; $j++) {
                            if (isset($sorted[$j])) {
                                $possibleEndTimestamp = $matchPointToTimestamp($sorted[$j]);
                                if ($sorted[$j]->end_distance <= $closestEndDistance && $possibleEndTimestamp > $startingTimestamp) {
                                    $closestEndIndex = $j;
                                    $closestEndTimestamp = $possibleEndTimestamp;
                                }
                            }
                        }
                        $segments[$routeFirstPoint->id]->performance[] = [
                            'start' => $startingTimestamp,
                            'end' => $closestEndTimestamp,
                        ];
                        // Mark these points as part of this segment. This can
                        // happen because sometimes there are a few points
                        // clustered around the start point of a segment, which
                        // may lead to duplicate matches.
                        foreach (range($routeFirstIdx, $closestEndIndex) as $trackIndex) {
                            $tracked[$routeFirstPoint->id][$trackIndex] = $trackIndex;
                        }
                        break;
                    }
                }
                unset($sorted[$routeFirstIdx]);
            }
        }
        return $segments;
    }

    /**
     * Undocumented function
     *
     * @param  boolean  $force
     * @return array
     */
    public function getLaps($force = false): array
    {
        $cacheKey = sprintf('activity:%d:laps', $this->id);
        if ($force) {
            Cache::forget($cacheKey);
        }
        return Cache::tags([sprintf('activity:%d', $this->id)])->remember($cacheKey, 3600, function () {
            $fit = new FitFile(Storage::path($this->upload->file_path));
            $laps = [];
            if (!empty($fit->data_mesgs['lap']['start_time']) && is_array($fit->data_mesgs['lap']['start_time'])) {
                foreach ($fit->data_mesgs['lap']['start_time'] as $index => $start_time) {
                    $laps[] = [
                        'start_time' => $start_time,
                        'end_time' => $fit->data_mesgs['lap']['start_time'][$index + 1] ?? array_pop($fit->data_mesgs['record']['timestamp']),
                        'avg_speed' => $fit->data_mesgs['lap']['avg_speed'][$index] ?? 0,
                        'avg_power' => $fit->data_mesgs['lap']['avg_power'][$index] ?? 0,
                        'avg_cadence' => $fit->data_mesgs['lap']['avg_cadence'][$index] ?? 0,
                        'normalized_power' => isset($fit->data_mesgs['lap']['avg_power'][$index]) ?
                            ($fit->data_mesgs['lap']['avg_power'][$index] !== $fit->data_mesgs['lap']['normalized_power'][$index] ? $fit->data_mesgs['lap']['normalized_power'][$index] : $fit->data_mesgs['lap']['avg_power'][$index])
                            : 0,
                        'avg_hr' => $fit->data_mesgs['lap']['avg_heart_rate'][$index] ?? 0,
                    ];
                }
            }
            return $laps;
        });
    }
}
