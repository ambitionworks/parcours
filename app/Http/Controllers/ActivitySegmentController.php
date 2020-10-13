<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Segment;
use App\Models\ActivitySegment;
use Illuminate\Http\Request;

class ActivitySegmentController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Activity $activity, Segment $segment, int $start)
    {
        // Get the exact segment record, as there may be repeats in an
        // activity.
        $segment = $activity->segments()
            ->where('segment_id', $segment->id)
            ->wherePivot('start_time', $start)
            ->first();

        return view('segments.activity', [
            'request' => $request,
            'activity' => $activity,
            'segment' => $segment,
            'pb' => ActivitySegment::segment($segment)
                ->byUser($activity->user)
                ->orderBy('elapsed', 'asc')
                ->first(),
        ]);
    }
}
