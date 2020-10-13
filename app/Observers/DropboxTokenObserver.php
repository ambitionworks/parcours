<?php

namespace App\Observers;

use App\Models\WahooDropboxProfile;
use Dcblogdev\Dropbox\Models\DropboxToken;

class DropboxTokenObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(DropboxToken $token)
    {
        WahooDropboxProfile::create(['user_id' => $token->user_id]);
    }
}
