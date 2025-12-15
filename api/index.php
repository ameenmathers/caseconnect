<?php

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $tmpPath = '/tmp';
    
    $directories = [
        $tmpPath . '/storage',
        $tmpPath . '/storage/framework',
        $tmpPath . '/storage/framework/views',
        $tmpPath . '/storage/framework/cache',
        $tmpPath . '/storage/framework/sessions',
        $tmpPath . '/storage/logs',
        $tmpPath . '/storage/app',
        $tmpPath . '/bootstrap/cache',
    ];

    foreach ($directories as $directory) {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    $bootstrapCache = __DIR__ . '/../bootstrap/cache';
    if (is_dir($bootstrapCache) && !is_writable($bootstrapCache)) {
        if (is_link($bootstrapCache)) {
            unlink($bootstrapCache);
        }
        if (!is_link($bootstrapCache)) {
            @symlink($tmpPath . '/bootstrap/cache', $bootstrapCache);
        }
    }
}

require __DIR__ . "/../public/index.php";
