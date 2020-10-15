<?php

namespace App\Jobs;

use ErrorException;
use App\Models\User;
use App\Models\ActivityUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Dcblogdev\Dropbox\Facades\Dropbox;

class SynchronizeWahooDropbox implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        if (!$this->user->wahoo_dropbox_profile->token) {
            throw new ErrorException('Attempting to synchonize when user does not have a token!');
        }

        Auth::login($this->user);

        $highwater = $this->user->wahoo_dropbox_profile->highwater;
        $next_highwater = $highwater;
        $searching = true;
        $cursor = false;
        do {
            if ($cursor) {
                $response = Dropbox::files()->listContentsContinue($cursor);
            } else {
                $response = Dropbox::files()->listContents('/apps/wahoofitness');
            }

            if (!$response['has_more']) {
                $searching = false;
            } else {
                $cursor = $response['cursor'];
            }

            if (is_array($response['entries'])) {
                foreach ($response['entries'] as $key => $entry) {
                    if (pathinfo($entry['name'])['extension'] === 'fit') {
                        $file_time = Carbon::parse($entry['client_modified']);
                        if ($file_time > $highwater) {
                            Log::warning(sprintf('Processing entry %d', $entry['id']));

                            $next_highwater = $file_time;
                            $path = 'wahoo_dropbox_activity/' . $entry['name'];

                            if (!Storage::exists($path)) {
                                Storage::put($path, Dropbox::files()->download($entry['path_lower']));

                                ActivityUpload::create(['file_path' => $path]);
                            }
                        }
                    }
                }
            }
        } while ($searching);

        Log::warning('Finished searching');

        if ($next_highwater > $highwater) {
            Log::warning(sprintf('New highwater is %s', $next_highwater));
            $this->user->wahoo_dropbox_profile->update(['highwater' => $next_highwater]);
        }
    }
}
