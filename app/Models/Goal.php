<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * User this Goal belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressAttribute($window = null)
    {
        if (!$window) {
            switch ($this->interval) {
                case 'weekly':
                    $lower = strtotime('monday this week');
                    $upper = strtotime('monday next week');
                    break;
                case 'monthly':
                    $lower = strtotime('first day of this month');
                    $upper = strtotime('last day of this month');
                    break;
                default:
                    return false;
            }
        }

        $progress = $this->user->activities()
            ->select($this->type)
            ->whereDate('performed_at', '>=', date('Y-m-d H:i:s', $lower))
            ->whereDate('performed_at', '<=', date('Y-m-d H:i:s', $upper))
            ->get()
            ->sum($this->type);

        return $progress;
    }
}
