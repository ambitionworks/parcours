<?php

namespace App\Console\Commands;

use App\Jobs\SynchronizeGarminConnect;
use App\Jobs\SynchronizeWahooDropbox;
use App\Models\User;
use Illuminate\Console\Command;

class SynchronizeActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parcours:activities:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize user activities';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::chunk(50, function ($users) {
            foreach ($users as $user) {
                SynchronizeGarminConnect::dispatchIf($user->garmin_connect_profile, $user);
                SynchronizeWahooDropbox::dispatchIf($user->wahoo_dropbox_profile, $user);
            }
        });
    }
}
