<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
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
        // Auto-migrate on first launch for NativePHP desktop+mobile (SQLite).
        // Skip during tests — RefreshDatabase handles migrations there.
        if (! app()->runningUnitTests() && ! Schema::hasTable('candidates')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable) {
                // Migration failed — table queries will handle it gracefully
            }
        }
    }
}
