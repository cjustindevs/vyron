<?php

declare(strict_types=1);

/**
 * TEMPORARY deep diagnostics for Render. DELETE after debugging.
 * Pure-composer inspection - does NOT boot Laravel (so it works even when boot fails).
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== COMPOSER / VENDOR INSPECTION ===\n\n";

require __DIR__ . '/../vendor/autoload.php';

echo "PHP: " . PHP_VERSION . "\n";
echo "SAPI: " . PHP_SAPI . "\n\n";

if (class_exists(Composer\InstalledVersions::class)) {
    foreach (['laravel/framework', 'illuminate/container', 'illuminate/filesystem', 'laravel/breeze'] as $pkg) {
        if (Composer\InstalledVersions::isInstalled($pkg)) {
            printf("%-25s %s  (ref %s)\n",
                $pkg,
                Composer\InstalledVersions::getPrettyVersion($pkg),
                substr(Composer\InstalledVersions::getReference($pkg) ?? '?', 0, 12)
            );
        } else {
            echo "$pkg: NOT INSTALLED\n";
        }
    }
} else {
    echo "InstalledVersions class: MISSING\n";
}

echo "\n--- class locations (autoload correctness) ---\n";

$classes = [
    'Illuminate\Foundation\Application',
    'Illuminate\Container\Container',
    'Illuminate\Filesystem\Filesystem',
    'Illuminate\Events\EventServiceProvider',
    'Illuminate\Foundation\Support\Providers\EventServiceProvider',
    'Illuminate\Http\Request',
];
foreach ($classes as $class) {
    if (class_exists($class)) {
        $file = (new ReflectionClass($class))->getFileName();
        echo "OK   $class\n     -> $file\n";
    } else {
        echo "MISSING $class\n";
    }
}

echo "\n--- app create (no boot) ---\n";
try {
    $app = require __DIR__ . '/../bootstrap/app.php';
    echo "app class: " . get_class($app) . "\n";
    echo "framework version(): " . $app->version() . "\n";
    echo "isAlias('files'):  " . var_export($app->isAlias('files'), true) . "\n";
    echo "bound('files'):    " . var_export($app->bound('files'), true) . "\n";
    echo "isAlias('events'): " . var_export($app->isAlias('events'), true) . "\n";
    echo "bound('events'):   " . var_export($app->bound('events'), true) . "\n";

    try {
        $files = $app->make('files');
        echo "make('files'):    " . get_class($files) . "\n";
    } catch (Throwable $e) {
        echo "make('files') FAILED: " . get_class($e) . ': ' . $e->getMessage() . "\n";
    }

    echo "\nloaded providers: " . implode(', ', array_keys($app->getLoadedProviders())) . "\n";
    echo "config/app providers: " . implode(', ', (array) ($app->make('config')->get('app.providers') ?? [])) . "\n";

    echo "\n--- boot attempt ---\n";
    try {
        $app->boot();
        echo "BOOT OK\n";
    } catch (Throwable $e) {
        echo "BOOT FATAL: " . get_class($e) . ': ' . $e->getMessage() . "\n";
        echo 'at ' . $e->getFile() . ':' . $e->getLine() . "\n";
    }
} catch (Throwable $e) {
    echo "APP CREATE FATAL: " . get_class($e) . ': ' . $e->getMessage() . "\n";
    echo 'at ' . $e->getFile() . ':' . $e->getLine() . "\n";
}

echo "\n--- bootstrap/cache files ---\n";
$dir = __DIR__ . '/../bootstrap/cache';
foreach (glob($dir . '/*.php') ?: [] as $f) {
    echo basename($f) . ' (' . filesize($f) . " bytes)\n";
}