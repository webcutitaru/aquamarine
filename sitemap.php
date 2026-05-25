<?php

declare(strict_types=1);

/** @var array<string, mixed> $config */
$config = require __DIR__ . '/includes/config.php';

$base = aquamarine_production_base_url($config);

$paths = [
    '/',
    '/index.php',
    '/filiale.php',
    '/curatatorie-profesionala-haine-balti.php',
    '/curatatorie-profesionala-haine-edinet.php',
    '/curatatorie-profesionala-haine-briceni.php',
    '/curatatorie-profesionala-haine-drochia.php',
    '/servicii-si-preturi.php',
    '/business.php',
    '/faq.php',
    '/contact.php',
    '/politica-confidentialitate.php',
    '/termeni.php',
];

header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($paths as $path) {
    $ro = $path === '/' ? $base . '/' : $base . $path;
    $ruPath = $path === '/' ? '/ru/' : '/ru' . $path;
    $ru = $base . $ruPath;

    echo '  <url><loc>' . htmlspecialchars($ro, ENT_XML1) . '</loc></url>' . "\n";
    echo '  <url><loc>' . htmlspecialchars($ru, ENT_XML1) . '</loc></url>' . "\n";
}

echo '</urlset>';
