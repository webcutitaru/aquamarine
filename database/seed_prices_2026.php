<?php

declare(strict_types=1);

/**
 * CLI: php database/seed_prices_2026.php
 * Importă catalogul prețuri 2026 din database/seed_prices_2026.sql
 * (alternativă la phpMyAdmin pe cPanel).
 */

$root = dirname(__DIR__);
require $root . '/includes/db.php';
require $root . '/includes/repository/pricing.php';

$pdo = aquamarine_pdo();
if (! $pdo instanceof PDO) {
    fwrite(STDERR, "Eroare: .env lipsește sau conexiunea DB a eșuat.\n");
    exit(1);
}

if (! pricing_db_has_ru_columns($pdo)) {
    fwrite(STDERR, "Eroare: lipsesc coloanele bilingve (_ru).\n");
    fwrite(STDERR, "Importați mai întâi: database/migrate_i18n_pricing.sql\n");
    exit(1);
}

$sqlFile = __DIR__ . '/seed_prices_2026.sql';
if (! is_readable($sqlFile)) {
    fwrite(STDERR, "Eroare: lipsește {$sqlFile}\n");
    exit(1);
}

$raw = file_get_contents($sqlFile);
if (! is_string($raw) || trim($raw) === '') {
    fwrite(STDERR, "Eroare: fișier SQL gol.\n");
    exit(1);
}

// Elimină comentariile pe linie (-- ...) ca parserul să nu se oprească la ; din text
$withoutComments = preg_replace('/^\s*--.*$/m', '', $raw) ?? $raw;

$statements = [];
$buffer = '';
$inString = false;
$stringChar = '';
$len = strlen($withoutComments);

for ($i = 0; $i < $len; ++$i) {
    $ch = $withoutComments[$i];
    $prev = $i > 0 ? $withoutComments[$i - 1] : '';

    if ($inString) {
        $buffer .= $ch;
        if ($ch === $stringChar && $prev !== '\\') {
            $inString = false;
            $stringChar = '';
        }
        continue;
    }

    if ($ch === '\'' || $ch === '"') {
        $inString = true;
        $stringChar = $ch;
        $buffer .= $ch;
        continue;
    }

    if ($ch === ';') {
        $stmt = trim($buffer);
        if ($stmt !== '') {
            $statements[] = $stmt;
        }
        $buffer = '';
        continue;
    }

    $buffer .= $ch;
}

$tail = trim($buffer);
if ($tail !== '') {
    $statements[] = $tail;
}

try {
    $pdo->beginTransaction();
    foreach ($statements as $sql) {
        if (preg_match('/^\s*SELECT\b/i', $sql) === 1) {
            $stmt = $pdo->query($sql);
            if ($stmt !== false) {
                $stmt->fetchAll();
            }
            continue;
        }
        $pdo->exec($sql);
    }
    $pdo->commit();

    $cats = (int) $pdo->query('SELECT COUNT(*) FROM price_categories')->fetchColumn();
    $items = (int) $pdo->query('SELECT COUNT(*) FROM price_items')->fetchColumn();
    echo "Import prețuri 2026 complet: {$cats} categorii, {$items} servicii.\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, 'Eroare import: ' . $e->getMessage() . "\n");
    exit(1);
}
