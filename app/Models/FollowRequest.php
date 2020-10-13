<?php

namespace App\Models;

use App\Notifications\UserNewFollowerRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowRequest extends Model
{
    use HasFactory;

    public static function booted()
    {
        static::created(function ($request) {
            $request->recipient->notify(new UserNewFollowerRequest($request));
        });
    }

    public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipient(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
