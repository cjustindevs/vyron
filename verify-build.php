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
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

if (! ($app['files'] instanceof Illuminate\Filesystem\Filesystem)) {
    fwrite(STDERR, "LARAVEL BOOT CHECK FAILED: files binding is wrong\n");
    exit(1);
}

echo "LARAVEL BOOT CHECK (via HttpKernel): OK\n";
exit(0);