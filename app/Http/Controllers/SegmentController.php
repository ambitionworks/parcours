<?php

namespace App\Http\Controllers;

use App\Actions\CreateSegment;
use App\Models\Activity;
use App\Models\Segment;
use Illuminate\Http\Request;


class SegmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function index(Activity $activity)
    {
        return view('segments.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function create(Activity $activity)
    {
        return view('segments.create', [
            'activity' => $activity,
            'geojson' => $activity->getGeoJson(null, null, false),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Activity $activity)
    {
        CreateSegment::create($request->toArray(), $activity);

        return redirect()->route('activities.show', $activity);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Segment  $segment
     * @return \Illuminate\Http\Response
     */
    public function show(Segment $segment)
    {
        return view('segments.show', [
            'segment' => $segment,
            'geojson' => $segment->activity->getGeoJson($segment->start_time, $segment->end_time, false),
        ]);
    }
}
