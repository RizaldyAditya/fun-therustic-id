<?php
namespace App\Console\Commands;

use App\Models\Donghua;
use App\Models\Stream;
use App\Observers\AnimekhorIndexObserver;
use App\Observers\AnimexinIndexObserver;
use App\Observers\DonghuastreamIndexObserver;
use App\Observers\DonghuaworldIndexObserver;
use Illuminate\Console\Command;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;

class CrawlIndexPage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:crawl-index {website} {--donghua_id=}';

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
        $input      = $this->argument('website');
        $donghua_id = $this->option('donghua_id');

        // get website
        $website = Stream::where(['label' => $input, 'is_crawlable' => true])->first();
        if (!$website) {
            $this->error("→ Website '{$input}' not found or not crawlable.");
            return 1;
        }

        // get donghua
        $donghua = Donghua::find($donghua_id);
        if (!$donghua) {
            $this->error("→ Donghua with ID '{$donghua_id}' not found.");
            return 1;
        }

        switch ($website->label) {
            case 'animexin':
                $url = $donghua->external_titles['animexin_url'] ?? null;
                if (empty($url)) {
                    $this->error("→ No AnimeXin URL found for this Donghua.");
                    return 1;
                }

                $this->info("Starting crawl: {$url}");
                Crawler::create()
                    ->ignoreRobots()
                    ->setUserAgent('Mozilla/5.0...')
                    ->setCrawlObserver(new AnimexinIndexObserver($donghua_id))
                    ->setCrawlProfile(new CrawlInternalUrls($url))
                    ->setMaximumDepth(0)
                    ->startCrawling($url);
                $this->info('Crawl has been completed.');
                break;
            case 'animekhor':
                $url = $donghua->external_titles['animekhor_url'] ?? null;
                if (empty($url)) {
                    $this->error("→ No AnimeKhor URL found for this Donghua.");
                    return 1;
                }

                $this->info("Starting crawl: {$url}");
                Crawler::create()
                    ->ignoreRobots()
                    ->setUserAgent('Mozilla/5.0...')
                    ->setCrawlObserver(new AnimekhorIndexObserver($donghua_id))
                    ->setCrawlProfile(new CrawlInternalUrls($url))
                    ->setMaximumDepth(0)
                    ->startCrawling($url);
                $this->info('Crawl has been completed.');
                break;
            case 'donghuastream':
                $url = $donghua->external_titles['donghuastream_url'] ?? null;
                if (empty($url)) {
                    $this->error("→ No DonghuaStream URL found for this Donghua.");
                    return 1;
                }

                $this->info("Starting crawl: {$url}");
                Crawler::create()
                    ->ignoreRobots()
                    ->setUserAgent('Mozilla/5.0...')
                    ->setCrawlObserver(new DonghuastreamIndexObserver($donghua_id))
                    ->setCrawlProfile(new CrawlInternalUrls($url))
                    ->setMaximumDepth(0)
                    ->startCrawling($url);
                $this->info('Crawl has been completed.');
                break;
            case 'donghuaworld':
                $url = $donghua->external_titles['donghuaworld_url'] ?? null;
                if (empty($url)) {
                    $this->error("→ No DonghuaWorld URL found for this Donghua.");
                    return 1;
                }

                $this->info("Starting crawl: {$url}");
                Crawler::create()
                    ->ignoreRobots()
                    ->setUserAgent('Mozilla/5.0...')
                    ->setCrawlObserver(new DonghuaworldIndexObserver($donghua_id))
                    ->setCrawlProfile(new CrawlInternalUrls($url))
                    ->setMaximumDepth(0)
                    ->startCrawling($url);
                $this->info('Crawl has been completed.');
                break;
        }
    }
}
