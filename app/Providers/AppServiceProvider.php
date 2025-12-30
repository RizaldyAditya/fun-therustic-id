<?php
namespace App\Providers;

use Filament\Support\Facades\FilamentTimezone;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentTimezone::set('Asia/Jakarta');

        // This will catch the error and log exactly which command was being run
        if (app()->runningInConsole()) {
            $cmd = implode(' ', $_SERVER['argv'] ?? []);
            if (str_contains($cmd, '--columns')) {
                \Illuminate\Support\Facades\Log::warning('FOUND THE CULPRIT: ' . $cmd);
            }
        }
    }
}
