<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AggregateWebsiteVisits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:aggregate-website-visits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge individual website visit records into daily aggregated documents by URL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting aggregation of website visits...');

        $countProcessed = 0;

        // Process records that are NOT aggregated yet
        // We use chunkById for safety when deleting during iteration
        \App\Models\WebsiteVisit::where('type', '!=', 'aggregated')
            ->orWhereNull('type')
            ->chunkById(500, function ($visits) use (&$countProcessed) {
                foreach ($visits as $visit) {
                    // Skip if no URL or date
                    if (!$visit->url || !$visit->visited_at) {
                        // Optionally delete these orphans if they aren't useful
                        $visit->delete();
                        continue;
                    }

                    $date = $visit->visited_at->toDateString();

                    // Perform the aggregation using a raw update to ensure atomicity and correct upsert behavior
                    \App\Models\WebsiteVisit::raw(function($collection) use ($visit, $date) {
                        $collection->updateOne(
                            [
                                'url' => $visit->url,
                                'visited_at_date' => $date,
                                'type' => 'aggregated'
                            ],
                            [
                                '$inc' => ['count' => 1],
                                '$set' => [
                                    'page_title' => $visit->page_title ?? 'Untitled',
                                    'visited_at' => $visit->visited_at, // Keep a timestamp for indexing/sorting
                                ]
                            ],
                            ['upsert' => true]
                        );
                    });

                    // Delete the individual record after it has been aggregated
                    $visit->delete();
                    $countProcessed++;
                }
                
                $this->info("Processed {$countProcessed} records...");
            });

        $this->info("Successfully aggregated {$countProcessed} website visit records.");
        return 0;
    }
}
