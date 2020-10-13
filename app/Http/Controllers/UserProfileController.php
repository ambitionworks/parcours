<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show(Request $request, User $user)
    {
        // dump(collect($user->activities()->whereNotNull('processed_at')->orderByDesc('performed_at')->limit(1)->get()));
        return view('profile.profile', [
            'request' => $request,
            'user' => $request->user(),
            'viewingUser' => $user,
            'activity' => collect($user->activities()->whereNotNull('processed_at')->orderByDesc('performed_at')->limit(1)->get()),
        ]);
    }
}
