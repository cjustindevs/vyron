<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

echo "PHP: " . PHP_VERSION . " (" . PHP_SAPI . ")\n";

$classes = [
    'Illuminate\Filesystem\Filesystem',
    'Illuminate\Foundation\Application',
    'Illuminate\Container\Container',
    'Illuminate\Events\Dispatcher',
];

foreach ($classes as $c) {
    if (! class_exists($c)) {
        fwrite(STDERR, "AUTOLOAD ERROR: {$c} missing\n");
        exit(1);
    }
}
echo "Autoload sanity check: OK\n";

echo "Application file: " . (new ReflectionClass(Illuminate\Foundation\Application::class))->getFileName() . "\n";
echo "Filesystem file:  " . (new ReflectionClass(Illuminate\Filesystem\Filesystem::class))->getFileName() . "\n";

$app = require __DIR__ . '/bootstrap/app.php';

echo "get_class(app): " . get_class($app) . "\n";
echo "app instanceof Illuminate\Foundation\Application: " . var_export($app instanceof Illuminate\Foundation\Application, true) . "\n";
echo "version(): " . $app->version() . "\n";

$rp = new ReflectionProperty($app, 'aliases');
$aliases = $rp->getValue($app);
echo "container aliases count: " . count($aliases) . "\n";
echo "aliases['files']: " . var_export($aliases['files'] ?? 'NOT SET', true) . "\n";
echo "isAlias('files'): " . var_export($app->isAlias('files'), true) . "\n";
echo "bound('files'): " . var_export($app->bound('files'), true) . "\n";
echo "bound('events'): " . var_export($app->bound('events'), true) . "\n";

echo "registered providers: " . implode(', ', array_keys($app->getLoadedProviders())) . "\n";

echo "--- included files (Application-related) ---\n";
foreach (get_included_files() as $f) {
    if (str_contains($f, 'Foundation/Application') || str_contains($f, 'Container.php')) {
        echo "  $f\n";
    }
}

echo "--- boot attempt ---\n";
try {
    $app->boot();
    echo "BOOT OK\n";
    echo ($app['files'] instanceof Illuminate\Filesystem\Filesystem) ? "files binding OK\n" : "files binding WRONG TYPE\n";
    exit(0);
} catch (Throwable $e) {
    echo "BOOT FATAL: " . get_class($e) . ': ' . $e->getMessage() . "\n";
    echo 'at ' . $e->getFile() . ':' . $e->getLine() . "\n";
    echo "--- post-fatal aliases ---\n";
    echo "aliases count: " . count($rp->getValue($app)) . "\n";
    echo "aliases['files']: " . var_export(($rp->getValue($app))['files'] ?? 'NOT SET', true) . "\n";
    exit(1);
}