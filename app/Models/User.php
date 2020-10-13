<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Multicaret\Acquaintances\Traits\CanFollow;
use Multicaret\Acquaintances\Traits\CanBeFollowed;
use Multicaret\Acquaintances\Traits\CanLike;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use CanFollow;
    use CanBeFollowed;
    use CanLike;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'default_timezone', 'slug', 'follower_preference'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            $slug = Str::slug($user->name);
            $iterations = 0;
            while (User::where('slug', $slug)->count()) {
                $slug = Str::slug($user->name) . ++$iterations;
            }
            $user->slug = $slug;
        });
    }

    /**
     * The user's activities.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * The user's goals.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Goal::class);
    }

    /**
     * The user's Garmin Connect profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function garmin_connect_profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(GarminConnectProfile::class);
    }

    /**
     * The user's Wahoo Dropbox profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function wahoo_dropbox_profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(WahooDropboxProfile::class);
    }

    /**
     * The user's metric profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function metric_profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MetricProfile::class);
    }

    /**
     * Undocumented function
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function follow_requests_received(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FollowRequest::class, 'recipient_id');
    }

    /**
     * Undocumented function
     *
     * @param User $sender
     * @return \App\Models\FollowRequest
     */
    public function followRequestFrom(User $sender)
    {
        return $this->follow_requests_received()->where('sender_id', $sender->id)->first();
    }

    /**
     * Undocumented function
     *
     * @param User $sender
     * @return boolean
     */
    public function hasFollowRequestFrom(User $sender): bool
    {
        return (bool) $this->follow_requests_received()->where('sender_id', $sender->id)->count();
    }
}
