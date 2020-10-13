<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;
use Multicaret\Acquaintances\Traits\CanBeLiked;

class Segment extends Model
{
    use PostgisTrait, CanBeLiked;

    /**
     * The attributes that are managed by the PostgisTrait trait.
     *
     * @var array
     */
    protected $postgisFields = [
        'start_point',
        'end_point',
        'route',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($segment) {
            ActivitySegment::withoutGlobalScopes()->segment($segment)->delete();
        });
    }

    /**
     * Activity this Segment belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activity(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * A user's ActivitySegments for a Segment.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_efforts(User $user = null): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        $user = $user ?? Auth::user();
        return $this->hasMany(ActivitySegment::class)->where('user_id', $user->id);
    }
}
