<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserIntegrationsController extends Controller
{
    public function show(Request $request)
    {
        return view('profile.integrations', [
            'request' => $request,
            'user' => $request->user(),
        ]);
    }
}
