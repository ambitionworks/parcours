<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Builder;

class ActivitySegment extends Pivot
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('fields', function (Builder $builder) {
            $builder->join('activities', 'activities.id', '=', 'activity_segment.activity_id');
            $builder->selectRaw('activity_segment.*');
            $builder->selectRaw('end_time - start_time AS elapsed');
            $builder->selectRaw('activities.user_id');
        });
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSegment(Builder $query, Segment $segment)
    {
        return $query->where('segment_id', $segment->id);
    }

    public function scopeByUser(Builder $query, User $user)
    {
        return $query->where('activities.user_id', $user->id);
    }
}