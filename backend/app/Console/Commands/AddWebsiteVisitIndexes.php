<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AddWebsiteVisitIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mongodb:add-indexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add indexes to MongoDB collections';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Adding indexes to website_visits collection...');

        Schema::connection('mongodb')->table('website_visits', function (Blueprint $collection) {
            $collection->index('visited_at');
            $collection->index('is_unique_visitor');
            $collection->index('is_bounce');
            $collection->index('url');
            $collection->index('device_type');
            $collection->index('visited_at_date');
            $collection->index('type');
            // Compound indexes for common queries
            $collection->index(['visited_at', 'is_unique_visitor']);
            $collection->index(['url', 'visited_at_date', 'type']);
        });

        $this->info('Indexes added successfully.');
    }
}
