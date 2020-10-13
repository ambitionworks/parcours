<?php

namespace App\Http\Controllers\Activity;

use App\Models\Activity;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use adriangibbons\phpFITFileAnalysis as FitFile;
use Illuminate\Support\Facades\Cache;

class PerformanceController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Activity $activity, int $start = null, int $end = null): \Illuminate\Http\JsonResponse
    {
        $callback = function ($start = null, $end = null) use ($activity) {
            $fit = new FitFile(Storage::path($activity->upload->file_path));

            $hr = $fit->data_mesgs['record']['heart_rate'] ?? [];
            $power = $fit->data_mesgs['record']['power'] ?? [];
            $speed = $fit->data_mesgs['record']['speed'] ?? [];
            $cadence = $fit->data_mesgs['record']['cadence'] ?? [];
            $timestamps = $fit->data_mesgs['record']['timestamp'] ?? [];

            if (!$start || !$end) {
                $start = $timestamps[0];
                $end = $timestamps[count($timestamps) - 1];
            }

            $output = ['power' => [], 'hr' => [], 'speed' => [], 'cadence' => []];

            for ($timestamp = $start; $timestamp <= $end; $timestamp++) {
                $output['hr'][] = $hr[$timestamp] ?? null;
                $output['power'][] = $power[$timestamp] ?? null;
                $output['speed'][] = $speed[$timestamp] ?? null;
                $output['cadence'][] = $cadence[$timestamp] ?? null;
            }

            return $output;
        };

        if (!$start || !$end) {
            $output = Cache::tags(sprintf('activity:%d', $activity->id))->remember(sprintf('activity:%d:performance', $activity->id), 3600, $callback);
        } else {
            $output = Cache::tags(sprintf('activity:%d', $activity->id))->remember(sprintf('activity:%d:performance:%d:%d', $activity->id, $start, $end), 3600, function() use ($callback, $start, $end) {
                return $callback($start, $end);
            });
        }
        $output = $callback($start, $end);

        return response()->json($output);
    }
}
