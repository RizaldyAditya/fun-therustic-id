<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CrawlAllStreams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:crawl-all-streams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl all stream websites for latest episodes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting crawl for all streams...');
        $this->info('This may take a while depending on the number of episodes.');
        $this->newLine();

        $commands = [
            'app:crawl-updates animexin',
            'app:crawl-updates donghuastream',
            'app:crawl-updates animekhor',
            'app:crawl-updates donghuaworld',
        ];

        foreach ($commands as $command) {
            $this->info("Running: {$command}");
            $this->call($command);
            $this->newLine();
        }

        $this->info('All crawls completed successfully!');
    }
}
