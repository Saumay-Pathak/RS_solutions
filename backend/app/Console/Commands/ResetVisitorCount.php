<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetVisitorCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-visitor-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all visitor data and start with a specific count';
    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 210956;
        
        $this->info("Truncating website_visits collection...");
        \App\Models\WebsiteVisit::truncate();

        $this->info("Creating initial aggregated record with count {$count}...");
        \App\Models\WebsiteVisit::create([
            'url' => '/',
            'page_title' => 'Home',
            'visited_at' => \Carbon\Carbon::now(),
            'visited_at_date' => \Carbon\Carbon::today()->toDateString(),
            'type' => 'aggregated',
            'count' => $count,
            'device_type' => 'desktop',
            'is_unique_visitor' => true
        ]);

        $this->info("Successfully reset visitor count.");
        
        $this->info("Updating dashboard statistics...");
        $this->call('dashboard:update-stats');

        return 0;
    }
}
