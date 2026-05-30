<?php

use App\Console\Commands\CrawlIndexPage;
use App\Console\Commands\CrawlLatestUpdate;
use App\Console\Commands\CrawlTest;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:crawl-test {--url=}', function () {
    $url = $this->option('url');
    $this->call(CrawlTest::class, ['--url' => $url]);
});

Artisan::command('app:crawl-updates {website} {--page=}', function () {
    $website = $this->argument('website');
    $page = $this->option('page') ?? false;
    $this->call(CrawlLatestUpdate::class, ['website' => $website, '--page' => $page]);
});

Artisan::command('app:crawl-index {website} {--donghua_id=}', function () {
    $website = $this->argument('website');
    $donghua_id = $this->option('donghua_id');
    $this->call(CrawlIndexPage::class, ['website' => $website, '--donghua_id' => $donghua_id]);
});

Schedule::command('app:crawl-updates animexin')->everyFifteenMinutes();

Schedule::command('app:crawl-updates animekhor')->hourlyAt(12);
Schedule::command('app:crawl-updates animekhor')->hourlyAt(24);
Schedule::command('app:crawl-updates animekhor')->hourlyAt(36);
Schedule::command('app:crawl-updates animekhor')->hourlyAt(48);
Schedule::command('app:crawl-updates animekhor')->hourlyAt(58);

Schedule::command('app:crawl-updates donghuastream')->hourlyAt(5);
Schedule::command('app:crawl-updates donghuastream')->hourlyAt(30);
Schedule::command('app:crawl-updates donghuastream')->hourlyAt(42);
Schedule::command('app:crawl-updates donghuastream')->hourlyAt(51);

Schedule::command('app:crawl-updates donghuaworld')->hourlyAt(17);
Schedule::command('app:crawl-updates donghuaworld')->hourlyAt(8);
Schedule::command('app:crawl-updates donghuaworld')->hourlyAt(18);
Schedule::command('app:crawl-updates donghuaworld')->hourlyAt(28);
Schedule::command('app:crawl-updates donghuaworld')->hourlyAt(40);
Schedule::command('app:crawl-updates donghuaworld')->hourlyAt(55);
