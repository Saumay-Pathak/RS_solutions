<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserActivity;
use Carbon\Carbon;

class CleanupUserActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-user-activities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete user activities older than 7 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::now()->subDays(7);

        $this->info("Deleting user activities older than {$date->toDateTimeString()}...");

        $deletedCount = UserActivity::where('occurred_at', '<', $date)->delete();

        $this->info("Successfully deleted {$deletedCount} old user activity records.");
    }
}
