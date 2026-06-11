<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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

        try {
            if (DB::connection()->getDriverName() === 'sqlite') {
                $dbPath = DB::connection()->getDatabaseName();
                if (file_exists($dbPath) && ! DB::connection()->getSchemaBuilder()->hasTable('candidates')) {
                    Artisan::call('migrate', ['--force' => true]);
                }
            }
        } catch (\Throwable) {
            // Database not available yet — will retry on next request
        }
    }
}