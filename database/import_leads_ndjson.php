<?php

declare(strict_types=1);

/**
 * CLI: php database/import_leads_ndjson.php
 */

$root = dirname(__DIR__);
require $root . '/includes/db.php';
require $root . '/includes/repository/leads.php';

$pdo = aquamarine_pdo();
if (! $pdo instanceof PDO) {
    fwrite(STDERR, "Eroare: config DB lipsă.\n");
    exit(1);
}

$path = $root . '/data/leads.ndjson';
if (! is_readable($path)) {
    echo "Nu există data/leads.ndjson — nimic de importat.\n";
    exit(0);
}

$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($lines === false) {
    fwrite(STDERR, "Nu pot citi leads.ndjson.\n");
    exit(1);
}

$check = $pdo->prepare(
    'SELECT id FROM leads WHERE legacy_ts <=> ? AND phone = ? AND name = ? LIMIT 1'
);
$insert = $pdo->prepare(
    'INSERT INTO leads (id, created_at, name, phone, email, service_interest, preferred_mag, message, attachments_json, ip_hashed, lang, status, legacy_ts)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);

$imported = 0;
$skipped = 0;

foreach ($lines as $line) {
    $row = json_decode($line, true);
    if (! is_array($row)) {
        continue;
    }

    $ts = (string) ($row['ts'] ?? '');
    $name = (string) ($row['name'] ?? '');
    $phone = (string) ($row['phone'] ?? '');
    if ($name === '' || $phone === '') {
        continue;
    }

    $check->execute([$ts !== '' ? $ts : null, $phone, $name]);
    if ($check->fetch()) {
        $skipped++;
        continue;
    }

    $createdAt = $ts !== '' ? date('Y-m-d H:i:s', strtotime($ts)) : gmdate('Y-m-d H:i:s');
    if ($createdAt === false) {
        $createdAt = gmdate('Y-m-d H:i:s');
    }

    $attachments = $row['attachments'] ?? null;
    $attachmentsJson = null;
    if (is_array($attachments) && $attachments !== []) {
        $attachmentsJson = json_encode($attachments, JSON_UNESCAPED_UNICODE);
    }

    $id = leads_generate_uuid();
    $insert->execute([
        $id,
        $createdAt,
        $name,
        $phone,
        $row['email'] ?? null,
        $row['service_interest'] ?? null,
        $row['preferred_mag'] ?? null,
        $row['message'] ?? null,
        $attachmentsJson,
        $row['ip_hashed'] ?? null,
        (string) ($row['lang'] ?? 'ro'),
        'new',
        $ts !== '' ? $ts : null,
    ]);
    $imported++;
}

echo "Import finalizat: {$imported} lead-uri noi, {$skipped} sărite (duplicate).\n";
