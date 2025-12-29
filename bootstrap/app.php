<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $bootstrapCache = __DIR__ . '/cache';
    $tmpBootstrapCache = '/tmp/bootstrap/cache';
    
    if (!is_dir($tmpBootstrapCache)) {
        mkdir($tmpBootstrapCache, 0755, true);
    }
    
    if (is_dir($bootstrapCache) && !is_writable($bootstrapCache)) {
        if (is_link($bootstrapCache)) {
            @unlink($bootstrapCache);
        }
        if (!is_link($bootstrapCache) && !file_exists($bootstrapCache)) {
            @symlink($tmpBootstrapCache, $bootstrapCache);
        }
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'error' => 'Authentication token is missing or invalid.',
                ], 401);
            }
        });
    })->create();
