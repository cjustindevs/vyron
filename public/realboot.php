<?php

declare(strict_types=1);

/**
 * REAL-request-path diagnostics: boots through the HTTP Kernel exactly like public/index.php.
 * This is the only faithful reproduction of the runtime 500. DELETE after debugging.
 */

header('Content-Type: text/plain; charset=utf-8');

require __DIR__ . '/../vendor/autoload.php';

try {
    $app = require __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    echo "bootstraping via HttpKernel (same as public/index.php)...\n";
    $kernel->bootstrap();

    echo "BOOT OK\n\n";

    echo "framework: " . $app->version() . "\n";
    echo "bound('files'):  " . var_export($app->bound('files'), true) . "\n";
    echo "bound('filesystem'): " . var_export($app->bound('filesystem'), true) . "\n";
    echo "files instance:  " . get_class($app['files']) . "\n\n";

    echo "APP_ENV:          " . $app->environment() . "\n";
    echo "APP_DEBUG:        " . var_export(env('APP_DEBUG'), true) . "\n";
    echo "APP_KEY set:      " . (env('APP_KEY') ? 'yes' : 'NO') . "\n";
    echo "SESSION_DRIVER:   " . env('SESSION_DRIVER', 'file') . "\n";
    echo "CACHE_STORE:      " . env('CACHE_STORE', 'file') . "\n";
    echo "QUEUE_CONNECTION: " . env('QUEUE_CONNECTION', 'database') . "\n\n";

    echo "DB_CONNECTION:    " . env('DB_CONNECTION', '(unset)') . "\n";
    echo "DB_HOST:          " . env('DB_HOST', '(unset)') . "\n";
    echo "DB_PORT:          " . env('DB_PORT', '(unset)') . "\n";
    echo "DB_DATABASE:      " . env('DB_DATABASE', '(unset)') . "\n";
    echo "DB_USERNAME:      " . env('DB_USERNAME', '(unset)') . "\n";
    echo "DB_PASSWORD set:  " . (env('DB_PASSWORD') ? 'yes' : 'no') . "\n\n";

    echo "--- DB connection test ---\n";
    try {
        $count = Illuminate\Support\Facades\DB::table('users')->count();
        echo "users table rows: {$count}\n";
    } catch (Throwable $e) {
        echo "DB FAILURE: " . get_class($e) . ': ' . $e->getMessage() . "\n";
        echo 'at ' . $e->getFile() . ':' . $e->getLine() . "\n";
    }

    echo "\n--- routes ---\n";
    echo "route count: " . count(Illuminate\Support\Facades\Route::getRoutes()->getRoutes()) . "\n";

    echo "\n--- rendering / (welcome) ---\n";
    try {
        $request = Illuminate\Http\Request::capture();
        $request->server->set('REQUEST_URI', '/');
        $request->server->set('SCRIPT_NAME', '/index.php');
        $response = $kernel->handle($request);
        echo "GET / -> HTTP " . $response->getStatusCode() . "\n";
        $body = trim(strip_tags(mb_substr($response->getContent(), 0, 400)));
        echo "body: " . $body . "\n";
        $kernel->terminate($request, $response);
    } catch (Throwable $e) {
        echo "GET / FAILED: " . get_class($e) . ': ' . $e->getMessage() . "\n";
        echo 'at ' . $e->getFile() . ':' . $e->getLine() . "\n";
    }
} catch (Throwable $e) {
    echo "FATAL: " . get_class($e) . ': ' . $e->getMessage() . "\n";
    echo 'at ' . $e->getFile() . ':' . $e->getLine() . "\n";
    foreach (array_slice($e->getTrace(), 0, 10) as $i => $f) {
        printf("#%d %s%s%s (%s:%s)\n",
            $i,
            $f['class'] ?? '',
            $f['type'] ?? '',
            $f['function'] ?? '',
            $f['file'] ?? 'unknown',
            $f['line'] ?? '?'
        );
    }
}