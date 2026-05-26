<?php

declare(strict_types=1);

require dirname(__DIR__) . '/init.php';
require_once __DIR__ . '/auth.php';

$pdo = aquamarine_pdo();
if (! $pdo instanceof PDO) {
    http_response_code(503);
    echo '<!DOCTYPE html><html lang="ro"><head><meta charset="utf-8"><title>Admin indisponibil</title></head><body>';
    echo '<p>Panoul admin necesită MySQL. Configurați <code>.env</code> (vezi <code>.env.example</code>) și rulați <code>database/schema.sql</code>.</p>';
    echo '</body></html>';
    exit;
}

$adminPageTitle = $adminPageTitle ?? 'Admin';
$adminCurrentNav = $adminCurrentNav ?? '';
