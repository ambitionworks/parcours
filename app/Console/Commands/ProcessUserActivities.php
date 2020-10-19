<?php

namespace App\Console\Commands;

use App\Jobs\ProcessActivity;
use App\Models\User;
use Illuminate\Console\Command;

class ProcessUserActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parcours:user:process {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process user activities';

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
        User::find($this->argument('user'))->activities()->chunk(50, function ($activities) {
            foreach ($activities as $activity) {
                ProcessActivity::dispatch($activity);
            }
        });
    }
}
