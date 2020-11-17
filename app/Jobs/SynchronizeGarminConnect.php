<?php

namespace App\Jobs;

use ErrorException;
use ZipArchive;
use App\Models\User;
use App\Models\ActivityUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use dawguk\GarminConnect;

class SynchronizeGarminConnect implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    private int $perPage = 50;

    public User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->user->garmin_connect_profile) {
            throw new ErrorException('Attempting to synchonize when user does not have a profile!');
        }

        Auth::login($this->user);

        // The "highwater" mark: The newest activity we have previously seen.
        $highwater = $this->user->garmin_connect_profile->highwater;
        $next_highwater = $highwater;
        $page = 0;
        $searching = true;
        do {
            Log::warning(sprintf('Requesting page %d', $page), ['user' => $this->user->id]);
            // Garmin Connect returns pages of activities, newest first.
            try {
                $activities = $this->user->garmin_connect_profile->api()->getActivityList($page * $this->perPage, $this->perPage, 'cycling');
            } catch (\Exception $e) {
                $searching = false;
            }

            if (isset($activities)) {
                // If it is no longer returning activities, we're done.
                if (!count($activities)) {
                    $searching = false;
                }
                foreach ($activities as $activity) {
                    // If this activity ID is less than or equal our highwater,
                    // we're now done.
                    if ($activity->activityId <= $highwater) {
                        $searching = false;
                    } else {
                        Log::warning(sprintf('Processing activityId %d', $activity->activityId));
                        // This is a processable activity.
                        // Record the potential new highwater value.
                        if ($activity->activityId > $next_highwater) {
                            $next_highwater = $activity->activityId;
                        }

                        $zipFile = 'garmin_connect_activity/' . $activity->activityId . '.zip';
                        $fitFile = 'garmin_connect_activity/' . $activity->activityId . '.fit';
                        $fitFileAlt = 'garmin_connect_activity/' . $activity->activityId . '_ACTIVITY.fit';
                        if (!Storage::exists($fitFile) && !Storage::exists($fitFileAlt)) {
                            Storage::put($zipFile, $this->user->garmin_connect_profile->api()->getDataFile(GarminConnect::DATA_TYPE_FIT, $activity->activityId));

                            $zip = new ZipArchive;
                            $zip->open(storage_path('app/' . $zipFile));
                            $zip->extractTo(storage_path('app/garmin_connect_activity/'));

                            Storage::delete($zipFile);

                            if (Storage::exists($fitFile)) {
                                ActivityUpload::create(['file_path' => $fitFile]);
                            } else if (Storage::exists($fitFileAlt)) {
                                ActivityUpload::create(['file_path' => $fitFileAlt]);
                            } else {
                                throw new \Exception('Missing .fit file.');
                            }
                        }
                    }
                }
                $page++;
            }
        } while ($searching);

        Log::warning('Finished searching');

        if ($next_highwater > $highwater) {
            Log::warning(sprintf('New highwater is %d', $next_highwater));
            $this->user->garmin_connect_profile->update(['highwater' => $next_highwater]);
        }
    }
}
