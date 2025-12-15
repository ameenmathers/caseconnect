<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->isVercel()) {
            $this->configureVercelStorage();
        }
    }

    public function boot(): void
    {
        //
    }

    protected function isVercel(): bool
    {
        return isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']);
    }

    protected function configureVercelStorage(): void
    {
        $storagePath = '/tmp/storage';

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
            mkdir($storagePath . '/framework/views', 0755, true);
            mkdir($storagePath . '/framework/cache', 0755, true);
            mkdir($storagePath . '/framework/sessions', 0755, true);
            mkdir($storagePath . '/logs', 0755, true);
            mkdir($storagePath . '/app', 0755, true);
        }

        $this->app->useStoragePath($storagePath);
    }
}
