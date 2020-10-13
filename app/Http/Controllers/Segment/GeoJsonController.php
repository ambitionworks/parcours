<?php

namespace App\Http\Controllers\Segment;

use App\Models\Segment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GeoJsonController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Segment  $segment
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Segment $segment): \Illuminate\Http\JsonResponse
    {
        $route = DB::table('segments')
            ->selectRaw('ST_asGeoJSON(route) AS route')
            ->where('id', $segment->id)
            ->first();

        $route = json_decode($route->route);
        $route->type = 'LineString';

        $output = ['features' => [], 'type' => 'FeatureCollection'];
        $output['features'][] = [
            'geometry' => $route,
            'type' => 'Feature',
        ];

        return response()->json($output);
    }
}
