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

        $directories = [
            $storagePath,
            $storagePath . '/framework',
            $storagePath . '/framework/views',
            $storagePath . '/framework/cache',
            $storagePath . '/framework/sessions',
            $storagePath . '/logs',
            $storagePath . '/app',
        ];

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }

        $this->app->useStoragePath($storagePath);
    }
}
