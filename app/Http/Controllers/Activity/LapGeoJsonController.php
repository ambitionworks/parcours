<?php

namespace App\Http\Controllers\Activity;

use App\Models\Activity;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use adriangibbons\phpFITFileAnalysis as FitFile;

class LapGeoJsonController extends Controller
{
    /**
     * Outputs geoJson for an activity lap.
     *
     * @param  \App\Activity  $activity
     * @param  int  $lap
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Activity $activity, int $lap): \Illuminate\Http\JsonResponse
    {
        $output = Cache::tags(sprintf('activity:%d', $activity->id))->remember(sprintf('activity:%d:lap-geojson:%d', $activity->id, $lap), 3600, function () use ($activity, $lap) {
            $output = ['features' => [], 'type' => 'FeatureCollection'];
            $fit = new FitFile(Storage::path($activity->upload->file_path));

            $alts = $fit->data_mesgs['record']['altitude'] ?? [];
            $lats = $fit->data_mesgs['record']['position_lat'] ?? null;
            $lons = $fit->data_mesgs['record']['position_long'] ?? null;

            $start = $fit->data_mesgs['lap']['start_time'][$lap];
            $end = $fit->data_mesgs['lap']['start_time'][$lap + 1] ?? array_pop($fit->data_mesgs['record']['timestamp']);

            if ($lats && $lons) {
                $last_lat_lon = [];
                $lat_lon = [];
                $timestamps = [];
                $altitude = [];

                for ($timestamp = $start; $timestamp <= $end; $timestamp++) {
                    if (isset($lats[$timestamp], $lons[$timestamp])) {
                        $lat_lon[] = $last_lat_lon = [$lons[$timestamp], $lats[$timestamp]];
                    } else {
                        $lat_lon[] = $last_lat_lon;
                    }
                    $timestamps[] = $timestamp;
                    $altitude[] = [$timestamp => $alts[$timestamp] ?? null];
                }

                $output['features'][] = [
                    'properties' => ['coordTimes' => $timestamps, 'altitude' => $altitude],
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

        return response()->json($output);
    }
}
