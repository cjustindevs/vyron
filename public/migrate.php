<?php

declare(strict_types=1);

/**
 * TEMPORARY one-shot migration runner for Render (no shell access).
 *
 * Usage:  GET /migrate.php?token=YOUR_MIGRATE_TOKEN
 *
 * SECURITY: refuses to run unless the MIGRATE_TOKEN environment variable is
 * set on the server AND matches the ?token= value. DELETE THIS FILE after use.
 */

$expectedToken = getenv('MIGRATE_TOKEN') ?: ($_ENV['MIGRATE_TOKEN'] ?? '');
$providedToken = $_GET['token'] ?? ($_SERVER['HTTP_X_MIGRATE_TOKEN'] ?? '');

if ($expectedToken === '' || !hash_equals($expectedToken, (string) $providedToken)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403 Forbidden: set MIGRATE_TOKEN in Render env vars and call with ?token=...');
}

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

header('Content-Type: text/plain; charset=utf-8');

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    $status = $kernel->call('migrate', ['--force' => true]);
    echo "=== MIGRATE (status $status) ===\n";
    echo $kernel->output();

    if (($seed = $_GET['seed'] ?? '') === '1') {
        $seedStatus = $kernel->call('db:seed', ['--force' => true]);
        echo "\n=== DB SEED (status $seedStatus) ===\n";
        echo $kernel->output();
    }

    echo "\n=== DONE ===\n";
    echo "IMPORTANT: DELETE public/migrate.php from the repository now.\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
    echo $kernel->output();
}