<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentTimezone;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
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
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        FilamentTimezone::set('Asia/Jakarta');

        // This will catch the error and log exactly which command was being run
        if (app()->runningInConsole()) {
            $cmd = implode(' ', $_SERVER['argv'] ?? []);
            if (str_contains($cmd, '--columns')) {
                Log::warning('FOUND THE CULPRIT: '.$cmd);
            }
        }
    }
}
