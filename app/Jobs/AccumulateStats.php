<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\Stat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AccumulateStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Activity model being stat'd.
     *
     * @var Activity $activity
     */
    public Activity $activity;

    /**
     * Operation being performed on the stats.
     */
    private string $operation;

    public const OP_ADD = 'add';
    public const OP_SUBTRACT = 'subtract';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Activity $activity, string $operation)
    {
        $this->activity = $activity;
        $this->operation = $operation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $performed_at = strtotime($this->activity->performed_at);
        $week = date('W', $performed_at);
        $month = date('n', $performed_at);
        $year = date('Y', $performed_at);

        $stats = $stat = Stat::where('user_id', $this->activity->user->id)
            ->where('week', $week)
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('type');

        foreach (Activity::$statable as $type) {
            if (!$stats->get($type) && $this->operation === self::OP_ADD) {
                $stat = new Stat(['week' => $week, 'month' => $month, 'year' => $year, 'type' => $type, 'value' => $this->activity->{$type} ?? 0]);
                $stat->user()->associate($this->activity->user);
                $stat->save();
            } else if ($stats->get($type)) {
                if ($this->operation === self::OP_ADD) {
                    $stats->get($type)->update(['value' => $stats->get($type)->value + $this->activity->{$type}]);
                } else if ($this->operation === self::OP_SUBTRACT) {
                    $stats->get($type)->update(['value' => $stats->get($type)->value - $this->activity->{$type}]);
                }
            }
        }

        if ($this->operation === self::OP_ADD) {
            $this->activity->update(['stats_accounted' => true]);
        }
    }
}
