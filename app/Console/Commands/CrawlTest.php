<?php
namespace App\Console\Commands;

use App\Observers\CrawlTestObserver;
use Illuminate\Console\Command;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;

class CrawlTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:crawl-test {--url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test crawl on a website with given URL.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = $this->option('url');
        $this->info('Starting crawl website : ' . $url);
        Crawler::create()
            ->ignoreRobots()
            ->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:146.0) Gecko/20100101 Firefox/146.0')
            ->setCrawlObserver(new CrawlTestObserver())
            ->setCrawlProfile(new CrawlInternalUrls($url))
            ->setMaximumDepth(0)
            ->startCrawling($url);
        $this->info('Crawl has been completed.');
    }
}
