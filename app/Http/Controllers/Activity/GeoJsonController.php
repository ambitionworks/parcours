<?php

namespace App\Http\Controllers\Activity;

use App\Models\Activity;
use App\Http\Controllers\Controller;

class GeoJsonController extends Controller
{
    /**
     * Outputs geoJson for the activity.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Activity $activity, int $start = null, int $end = null): \Illuminate\Http\JsonResponse
    {
        return response()->json($activity->getGeoJson($start, $end));
    }
}
