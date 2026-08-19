<?php

header('Content-Type: text/plain; charset=utf-8');

function maskKey(?string $k): string
{
    $k = (string) $k;
    return $k === '' ? '(empty)' : substr($k, 0, 6) . '...' . substr($k, -4);
}

echo "=== GROQ DIAGNOSTIC ===\n\n";

$platform = [
    'GROQ_MODEL'    => getenv('GROQ_MODEL') ?: ($_ENV['GROQ_MODEL'] ?? ($_SERVER['GROQ_MODEL'] ?? null)),
    'GROQ_API_URL'  => getenv('GROQ_API_URL') ?: ($_ENV['GROQ_API_URL'] ?? ($_SERVER['GROQ_API_URL'] ?? null)),
    'GROQ_API_KEY'  => getenv('GROQ_API_KEY') ?: ($_ENV['GROQ_API_KEY'] ?? ($_SERVER['GROQ_API_KEY'] ?? null)),
    'APP_ENV'       => getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? ($_SERVER['APP_ENV'] ?? null)),
];

echo "PLATFORM (Render) env vars:\n";
foreach ($platform as $k => $v) {
    echo "  $k = " . ($k === 'GROQ_API_KEY' ? maskKey($v) : var_export($v, true)) . "\n";
}

$dotenv = [];
if (file_exists(__DIR__ . '/../.env')) {
    foreach (file(__DIR__ . '/../.env') as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$k, $v] = explode('=', $line, 2);
        $dotenv[$k] = trim($v);
    }
}

echo "\n.env file values:\n";
echo "  GROQ_MODEL   = " . var_export($dotenv['GROQ_MODEL'] ?? null, true) . "\n";
echo "  GROQ_API_URL = " . var_export($dotenv['GROQ_API_URL'] ?? null, true) . "\n";
echo "  GROQ_API_KEY = " . maskKey($dotenv['GROQ_API_KEY'] ?? null) . "\n";

$model    = $platform['GROQ_MODEL']    ?: ($dotenv['GROQ_MODEL']    ?? 'openai/gpt-oss-120b');
$apiUrl   = $platform['GROQ_API_URL']  ?: ($dotenv['GROQ_API_URL']  ?? 'https://api.groq.com/openai/v1/chat/completions');
$apiKey   = $platform['GROQ_API_KEY']  ?: ($dotenv['GROQ_API_KEY']  ?? '');

echo "\n=== RESOLVED VALUES (what Laravel would use) ===\n";
echo "  MODEL   = $model\n";
echo "  API_URL = $apiUrl\n";
echo "  KEY     = " . maskKey($apiKey) . "\n\n";

if ($apiKey === '') {
    echo "FATAL: no GROQ_API_KEY resolved\n";
    exit(1);
}

$payload = json_encode([
    'model' => $model,
    'messages' => [['role' => 'user', 'content' => 'Say OK']],
    'max_tokens' => 10,
]);

$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $apiKey, 'Content-Type: application/json'],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
]);

$resp = curl_exec($ch);
$status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

echo "=== LIVE TEST (POST $apiUrl, model=$model) ===\n";
echo "HTTP $status\n";
if ($resp !== false) {
    $decoded = json_decode($resp, true);
    if (isset($decoded['error'])) {
        echo "GROQ ERROR: " . json_encode($decoded['error'], JSON_PRETTY_PRINT) . "\n";
    } else {
        $content = (string) ($decoded['choices'][0]['message']['content'] ?? '(no content)');
        echo "SUCCESS, content: " . substr($content, 0, 100) . "\n";
    }
} else {
    echo "CURL ERROR: $err\n";
}
