<?php

namespace App\Console\Commands;

use Spatie\Crawler\Crawler;
use Illuminate\Console\Command;
use App\Observers\EpisodeObserver;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;

class CrawlIndexPage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:crawl-index-page {website_label} {--donghua_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get episode list of donghua title and episode number';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $website_label   = $this->argument('website_label');
        $donghua_id = $this->option('donghua_id') ?? false;

        if (!empty($donghua_id)) {
            $this->error('Donghua id is required');
            return 1;
        }

        $this->info('Starting crawl website : ' . $website_label);
        Crawler::create()
            ->ignoreRobots()
            ->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:146.0) Gecko/20100101 Firefox/146.0')
            // ->setCrawlObserver(new EpisodeObserver())
            ->setCrawlProfile(new CrawlInternalUrls($website_label))
            ->setMaximumDepth(0)
            ->startCrawling($website_label);
        $this->info('Crawl has been completed.');
    }
}
