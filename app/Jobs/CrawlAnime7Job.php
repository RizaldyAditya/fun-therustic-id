<?php

namespace App\Jobs;

use App\Console\Commands\CrawlAnime7;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;

class CrawlAnime7Job implements ShouldQueue
{
    use Queueable;

    public int $timeout = 120;

    public function handle(): void
    {
        Artisan::call(CrawlAnime7::class);
    }
}
