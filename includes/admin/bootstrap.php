<?php

declare(strict_types=1);

require dirname(__DIR__) . '/init.php';
require_once __DIR__ . '/auth.php';

$diagnose = aquamarine_db_diagnose();
if (! $diagnose['ok']) {
    http_response_code(503);
    echo '<!DOCTYPE html><html lang="ro"><head><meta charset="utf-8"><title>Admin indisponibil</title></head><body>';
    echo '<p><strong>Panoul admin necesită MySQL.</strong></p>';
    echo '<p>' . $diagnose['message'] . '</p>';
    if (($diagnose['details'] ?? []) !== []) {
        echo '<pre style="background:#f4f4f4;padding:1em;overflow:auto">';
        foreach ($diagnose['details'] as $line) {
            echo esc($line) . "\n";
        }
        echo '</pre>';
    }
    echo '<p>Diagnostic CLI: <code>php database/check_setup.php</code></p>';
    echo '<p>După ce conexiunea merge, importați <code>database/schema.sql</code> în phpMyAdmin și rulați <code>php database/create_admin.php</code>.</p>';
    echo '</body></html>';
    exit;
}

$pdo = aquamarine_pdo();
if (! $pdo instanceof PDO) {
    http_response_code(503);
    echo '<!DOCTYPE html><html lang="ro"><head><meta charset="utf-8"><title>Admin indisponibil</title></head><body>';
    echo '<p>Conexiunea MySQL a eșuat după verificare. Reîncărcați pagina; dacă persistă, verificați <code>error_log</code> pe server.</p>';
    echo '</body></html>';
    exit;
}

$adminPageTitle = $adminPageTitle ?? 'Admin';
$adminCurrentNav = $adminCurrentNav ?? '';
