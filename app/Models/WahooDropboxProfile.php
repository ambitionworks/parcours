<?php

namespace App\Models;

use Dcblogdev\Dropbox\Models\DropboxToken;
use Illuminate\Database\Eloquent\Model;

class WahooDropboxProfile extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function token()
    {
        return $this->hasOne(DropboxToken::class, 'user_id', 'user_id');
    }
}
