<?php

namespace App\Console\Commands;

use App\Observers\AnimekhorObserver;
use App\Observers\AnimexinObserver;
use App\Observers\DonghuastreamObserver;
use App\Observers\DonghuaworldObserver;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\DB;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;

class CrawlLatestUpdate extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     * Example : php artisan app:crawl-update animexin --page=2
     *
     * @var string
     */
    protected $signature = 'app:crawl-updates {website} {--page=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl donghua website and fetch episodes data.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $input = $this->argument('website');
        $website = (array) DB::table('streams')->where(['label' => $input, 'is_crawlable' => true])->first();
        if (empty($website)) {
            $this->error('→ Website with label "'.$input.'" not found on database or not crawlable.');

            return 1;
        }

        switch ($website['label']) {
            case 'animexin':
                $url = $website['homepage_url'];
                $page = $this->option('page') ?? false;
                if ($page) {
                    $url = $url."page/$page/";
                }

                $this->info('Starting crawl website : '.$url);
                Crawler::create()
                    ->ignoreRobots()
                    ->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:146.0) Gecko/20100101 Firefox/146.0')
                    ->setCrawlObserver(new AnimexinObserver)
                    ->setCrawlProfile(new CrawlInternalUrls($url))
                    ->setMaximumDepth(0)
                    ->startCrawling($url);
                $this->info('Crawl has been completed.');
                break;
            case 'animekhor':
                $url = $website['homepage_url'];
                $page = $this->option('page') ?? false;
                if ($page) {
                    $url = $url."page/$page/";
                }

                $this->info('Starting crawl website : '.$url);
                Crawler::create()
                    ->ignoreRobots()
                    ->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:146.0) Gecko/20100101 Firefox/146.0')
                    ->setCrawlObserver(new AnimekhorObserver)
                    ->setCrawlProfile(new CrawlInternalUrls($url))
                    ->setMaximumDepth(0)
                    ->startCrawling($url);
                $this->info('Crawl has been completed.');
                break;
            case 'donghuastream':
                $url = $website['homepage_url'];
                $page = $this->option('page') ?? false;
                if ($page) {
                    $url = $url."page/$page/";
                }

                $this->info('Starting crawl website : '.$url);
                Crawler::create()
                    ->ignoreRobots()
                    ->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:146.0) Gecko/20100101 Firefox/146.0')
                    ->setCrawlObserver(new DonghuastreamObserver)
                    ->setCrawlProfile(new CrawlInternalUrls($url))
                    ->setMaximumDepth(0)
                    ->startCrawling($url);
                $this->info('Crawl has been completed.');
                break;
            case 'donghuaworld':
                $url = $website['homepage_url'];
                $page = $this->option('page') ?? false;
                if ($page) {
                    $url = $url."page/$page/";
                }

                $this->info('Starting crawl website : '.$url);
                Crawler::create()
                    ->ignoreRobots()
                    ->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:146.0) Gecko/20100101 Firefox/146.0')
                    ->setCrawlObserver(new DonghuaworldObserver)
                    ->setCrawlProfile(new CrawlInternalUrls($url))
                    ->setMaximumDepth(0)
                    ->startCrawling($url);
                $this->info('Crawl has been completed.');
                break;
        }
    }
}
