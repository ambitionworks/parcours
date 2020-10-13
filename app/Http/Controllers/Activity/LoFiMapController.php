<?php

namespace App\Http\Controllers\Activity;

use App\Models\Activity;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class LoFiMapController extends Controller
{
    private CONST PLACEHOLDER = 'lofi_map_placeholder.png';

    /**
     * Handle the incoming request.
     *
     * @param  \App\Activity  $activity
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke(Activity $activity): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $exists = false;
        $filename = sprintf('activity_lofi_maps/%s.png', $this->hashFileName($activity->id));

        if (!$activity->stationary) {
            if (!Storage::exists($filename)) {
                // Remove repeat points from the route, turn it in to a line,
                // then simplify it so we (hopefully) don't hit GET URL length
                // limits.
                $points = DB::select(
                    DB::raw(
                        'SELECT
                            ST_AsGeoJSON(
                                ST_Simplify(
                                    ST_MakeLine(
                                        ST_RemoveRepeatedPoints(geometry(route))
                                    ),
                                0.0009)
                            ) AS route FROM activities WHERE id = ?'
                    ),
                    [$activity->id]
                );
                $geojson = [
                    'geometry' => json_decode($points[0]->route),
                    'type' => 'Feature',
                    'properties' => [
                        'stroke' => '#5588fc',
                    ],
                ];

                $response = Http::get(
                    sprintf('https://api.mapbox.com/styles/v1/mapbox/dark-v10/static/geojson(%s)/auto/400x200@2x?access_token=%s',
                        urlencode(json_encode($geojson)),
                        config('parcours.osm_key')
                    )
                );

                if ($response->ok()) {
                    Storage::put($filename, $response->body());
                    $exists = true;
                }
            } else {
                $exists = true;
            }
        }

        return response()->download(storage_path('app/' . ($exists ? $filename : self::PLACEHOLDER)));
    }

    private function hashFileName(int $id)
    {
        return md5($id . 'qa9sRc3ehRyUgRive2XopNLo');
    }
}
