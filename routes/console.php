<?php

use App\Console\Commands\CrawlIndexPage;
use App\Console\Commands\CrawlLatestUpdate;
use App\Console\Commands\CrawlTest;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:crawl-test {--url=}', function () {
    $url = $this->option('url');
    $this->call(CrawlTest::class, ['--url' => $url]);
});

Artisan::command('app:crawl-updates {website} {--page=}', function () {
    $website = $this->argument('website');
    $page    = $this->option('page') ?? false;
    $this->call(CrawlLatestUpdate::class, ['website' => $website, '--page' => $page]);
});

Artisan::command('app:crawl-index {website} {--donghua_id=}', function () {
    $website    = $this->argument('website');
    $donghua_id = $this->option('donghua_id');
    $this->call(CrawlIndexPage::class, ['website' => $website, '--donghua_id' => $donghua_id]);
});
