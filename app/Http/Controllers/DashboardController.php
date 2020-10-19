<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return view('dashboard', [
            'user' => $request->user(),
            'activities' => Activity::whereNotNull('processed_at')
                ->whereIn('user_id', $request->user()->followings()->get()->pluck('id'))
                ->orderByDesc('performed_at')->simplePaginate(10),
            'latest' => $request->user()->activities()->orderBy('performed_at', 'desc')->first(),
        ]);
    }
}
