<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

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

$app = require __DIR__ . '/bootstrap/app.php';
$app->boot();

if (! ($app['files'] instanceof Illuminate\Filesystem\Filesystem)) {
    fwrite(STDERR, "LARAVEL BOOT CHECK FAILED: files binding is wrong\n");
    exit(1);
}

echo "LARAVEL BOOT CHECK: OK\n";
exit(0);