<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrawlerController;

Route::get('/crawler', [CrawlerController::class, 'index'])->name('crawler.index');
Route::post('/crawler/crawl', [CrawlerController::class, 'crawl'])->name('crawler.crawl');
