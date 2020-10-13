<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserFollowersController extends Controller
{
    public function show(Request $request)
    {
        return view('profile.followers', [
            'request' => $request,
            'user' => $request->user(),
        ]);
    }
}
