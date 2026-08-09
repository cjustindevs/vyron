<?php

declare(strict_types=1);

/**
 * TEMPORARY diagnostic page for Render. DELETE after debugging.
 * Visit: https://vyron.onrender.com/debug.php
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== VYRON DIAGNOSTICS ===\n\n";

try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require __DIR__ . '/../bootstrap/app.php';
    $app->boot();

    echo "PHP version:        " . PHP_VERSION . "\n";
    echo "Laravel version:    " . $app->version() . "\n";
    echo "APP_ENV:            " . env('APP_ENV', '(unset)') . "\n";
    echo "APP_DEBUG:          " . var_export(env('APP_DEBUG'), true) . "\n";
    echo "APP_URL:            " . env('APP_URL', '(unset)') . "\n";
    echo "APP_KEY set:        " . (env('APP_KEY') ? 'yes' : 'NO - sessions/encryption will fail') . "\n\n";

    echo "SESSION_DRIVER:     " . env('SESSION_DRIVER', 'file') . "\n";
    echo "CACHE_STORE:        " . env('CACHE_STORE', 'file') . "\n";
    echo "QUEUE_CONNECTION:   " . env('QUEUE_CONNECTION', 'database') . "\n";
    echo "LOG_CHANNEL:        " . env('LOG_CHANNEL', 'stack') . "\n\n";

    echo "DB_CONNECTION:      " . env('DB_CONNECTION', '(unset)') . "\n";
    echo "DB_HOST:            " . env('DB_HOST', '(unset)') . "\n";
    echo "DB_PORT:            " . env('DB_PORT', '(unset)') . "\n";
    echo "DB_DATABASE:        " . env('DB_DATABASE', '(unset)') . "\n";
    echo "DB_USERNAME:        " . env('DB_USERNAME', '(unset)') . "\n";
    echo "DB_PASSWORD set:    " . (env('DB_PASSWORD') ? 'yes' : 'no') . "\n\n";

    echo "--- PostgreSQL driver check ---\n";
    echo (extension_loaded('pdo_pgsql') ? 'pdo_pgsql: LOADED' : 'pdo_pgsql: MISSING') . "\n";
    echo (extension_loaded('pdo_mysql') ? 'pdo_mysql: LOADED' : 'pdo_mysql: MISSING') . "\n\n";

    echo "--- DB connection test ---\n";
    try {
        $count = DB::table('users')->count();
        echo "Connected to '" . env('DB_DATABASE') . "'. users table rows: {$count}\n";
    } catch (Throwable $e) {
        echo "DB FAILURE: " . get_class($e) . ': ' . $e->getMessage() . "\n";
    }

    echo "\n--- Routing / app boot ---\n";
    echo "Routes loaded:     " . count(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes()) . "\n";
    echo "OK - app boots fine.\n";
} catch (Throwable $e) {
    echo "FATAL: " . get_class($e) . ': ' . $e->getMessage() . "\n";
    echo 'at ' . $e->getFile() . ':' . $e->getLine() . "\n\n";
    echo "--- trace (first 15 frames) ---\n";
    $frames = array_slice($e->getTrace(), 0, 15);
    foreach ($frames as $i => $f) {
        printf("#%d %s%s%s(%s)\n",
            $i,
            $f['class'] ?? '',
            $f['type'] ?? '',
            $f['function'] ?? '',
            ($f['file'] ?? '') . ':' . ($f['line'] ?? '')
        );
    }
}