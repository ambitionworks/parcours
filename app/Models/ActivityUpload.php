<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ActivityUpload extends Model
{
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
        static::created(function ($activity_upload) {
            $activity = new Activity;
            $activity->upload()->associate($activity_upload);
            $activity->save();
        });

        static::deleting(function ($activity_upload) {
            Storage::delete($activity_upload->file_path);
        });
    }

    public function activity()
    {
        return $this->hasOne(Activity::class, 'activity_upload_id');
    }
}
