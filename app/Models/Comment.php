<?php

namespace App\Models;

use App\Notifications\ActivityNewComment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Markdown;

class Comment extends Model
{
    use HasFactory;

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
        static::created(function ($comment) {
            switch (get_class($comment->commentable)) {
                case (Activity::class):
                    $comment->commentable->user->notify(new ActivityNewComment($comment));
                    break;
            }
        });
    }

    /**
     * Get the owning commentable model.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * User this Comment belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCommentAttribute($value)
    {
        return Markdown::parse($value);
    }
}
