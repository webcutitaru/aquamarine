<?php

declare(strict_types=1);

/**
 * CLI: php database/check_setup.php
 * Web: https://domeniu.md/database/check_setup.php (ștergeți accesul după setup)
 */

$root = dirname(__DIR__);
require_once $root . '/includes/env.php';
aquamarine_load_env();

$isCli = PHP_SAPI === 'cli';
if (! $isCli) {
    $allowWeb = filter_var(aquamarine_env('ALLOW_SETUP_CHECK', 'false') ?? 'false', FILTER_VALIDATE_BOOLEAN);
    if (! $allowWeb) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=utf-8');
        echo "Acces interzis. Rulați: php database/check_setup.php\n";
        exit;
    }
    header('Content-Type: text/plain; charset=utf-8');
}

require $root . '/includes/db.php';

$out = static function (string $line) use ($isCli): void {
    if ($isCli) {
        echo $line . PHP_EOL;
    } else {
        echo $line . "\n";
    }
};

$fail = static function (string $line, int $code = 1) use ($isCli): never {
    if ($isCli) {
        fwrite(STDERR, $line . PHP_EOL);
    } else {
        echo 'FAIL: ' . $line . "\n";
    }
    exit($code);
};

$diagnose = aquamarine_db_diagnose();
foreach ($diagnose['details'] ?? [] as $line) {
    $out($line);
}

if (! $diagnose['ok']) {
    $msg = strip_tags(str_replace(['<code>', '</code>'], '', $diagnose['message']));
    $fail($msg);
}

$requiredTables = [
    'admin_user',
    'price_settings',
    'price_categories',
    'price_items',
    'homepage_offers',
    'leads',
];

$pdo = aquamarine_pdo();
if (! $pdo instanceof PDO) {
    $fail('conexiunea MySQL a eșuat după diagnostic');
}

$out('OK: conexiune MySQL');

$missing = [];
foreach ($requiredTables as $table) {
    $stmt = $pdo->query(
        'SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ' . $pdo->quote($table)
    );
    if ($stmt === false || $stmt->fetchColumn() === false) {
        $missing[] = $table;
    }
}

if ($missing !== []) {
    $fail('tabele lipsă: ' . implode(', ', $missing) . ' — importați database/schema.sql');
}

$out('OK: toate tabelele necesare există');

$adminCount = (int) $pdo->query('SELECT COUNT(*) FROM admin_user')->fetchColumn();
if ($adminCount === 0) {
    if ($isCli) {
        fwrite(STDERR, "WARN: niciun cont admin — rulați: php database/create_admin.php\n");
    } else {
        echo "WARN: niciun cont admin — rulați: php database/create_admin.php\n";
    }
    exit(2);
}

$out("OK: {$adminCount} cont(uri) admin");

$priceCats = (int) $pdo->query('SELECT COUNT(*) FROM price_categories')->fetchColumn();
$priceItems = (int) $pdo->query('SELECT COUNT(*) FROM price_items')->fetchColumn();
$out("INFO: catalog prețuri — {$priceCats} categorii, {$priceItems} servicii");
if ($priceItems === 0) {
    if ($isCli) {
        fwrite(STDERR, "WARN: catalog prețuri gol — importați database/seed_prices_2026.sql sau rulați: php database/seed_prices_2026.php\n");
    } else {
        echo "WARN: catalog prețuri gol — importați database/seed_prices_2026.sql\n";
    }
    exit(2);
}

exit(0);
