<?php

namespace App\Http\Controllers\Activity;

use App\Models\Activity;
use App\Jobs\ProcessActivity;
use App\Http\Controllers\Controller;

class ProcessController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Activity $activity)
    {
        ProcessActivity::dispatch($activity);
        return redirect()->route('activities.show', $activity);
    }
}
