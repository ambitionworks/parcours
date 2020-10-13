<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserMetricsController extends Controller
{
    public function show(Request $request)
    {
        return view('profile.metrics', [
            'request' => $request,
            'user' => $request->user(),
        ]);
    }
}
