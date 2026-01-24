<?php

use App\Http\Controllers\DeploymentController;
use App\Http\Controllers\DonghuaApiController;
use Illuminate\Support\Facades\Route;

Route::post('/git-deploy-webhook', [DeploymentController::class, 'deploy'])->name('github.webhook');

Route::prefix('v1')->group(function () {
    // mobile app / tv app (currently dropped)
    Route::get('/search', [DonghuaApiController::class, 'search']);
    Route::get('/latest', [DonghuaApiController::class, 'latest']);
    Route::get('/for-you', [DonghuaApiController::class, 'forYou']);
    Route::get('/trending', [DonghuaApiController::class, 'trending']);
    Route::get('/donghua', [DonghuaApiController::class, 'show']);
    
    // swiftUI downloader app
    Route::get('/latest-donghua-episodes', [DonghuaApiController::class, 'latestEpisodes']);
    Route::post('/downloader-json', [DonghuaApiController::class, 'downloaderJson']);
    Route::post('/update/episode-dl', [DonghuaApiController::class, 'updateEpisodeDL']);
});
