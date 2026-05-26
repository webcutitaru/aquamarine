<?php

declare(strict_types=1);

/**
 * CLI: php database/seed_from_json.php
 * Importă preturi.json și slide-urile implicite în MySQL.
 */

$root = dirname(__DIR__);
require $root . '/includes/db.php';
require $root . '/includes/repository/pricing.php';
require $root . '/includes/repository/offers.php';

$pdo = aquamarine_pdo();
if (! $pdo instanceof PDO) {
    fwrite(STDERR, "Eroare: .env lipsește sau conexiunea DB a eșuat.\n");
    exit(1);
}

$catalog = pricing_fetch_catalog_from_json();
$pdo->beginTransaction();

try {
    $pdo->exec('DELETE FROM price_items');
    $pdo->exec('DELETE FROM price_categories');

    pricing_save_settings($pdo, $catalog['note'], $catalog['currency']);

    $catOrder = 0;
    foreach ($catalog['categories'] as $cat) {
        if (! is_array($cat)) {
            continue;
        }
        $name = (string) ($cat['name'] ?? '');
        if ($name === '') {
            continue;
        }
        $footnote = isset($cat['footnote']) ? (string) $cat['footnote'] : null;
        $catId = pricing_create_category($pdo, $name, $footnote, $catOrder++);

        $itemOrder = 0;
        $items = isset($cat['items']) && is_array($cat['items']) ? $cat['items'] : [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $service = (string) ($item['service'] ?? '');
            $price = (string) ($item['price'] ?? '');
            if ($service === '' || $price === '') {
                continue;
            }
            pricing_create_item(
                $pdo,
                $catId,
                $service,
                $price,
                isset($item['description']) ? (string) $item['description'] : null,
                isset($item['note']) ? (string) $item['note'] : null,
                $itemOrder++
            );
        }
    }

    $countOffers = (int) $pdo->query('SELECT COUNT(*) FROM homepage_offers')->fetchColumn();
    if ($countOffers === 0) {
        $defaults = offers_default_slides();
        $content = offers_default_content_seed();
        $order = 0;
        $seedImages = [
            'assets/images/oferte/aquamarine_cleaning_system.webp',
            'assets/images/oferte/aquamarine_cleaning_system_slider.webp',
        ];
        foreach ($defaults as $idx => $slide) {
            offers_create(
                $pdo,
                $seedImages[$idx] ?? 'assets/images/oferte/aquamarine_cleaning_system.webp',
                (string) $slide['alt'],
                (string) $slide['href'],
                true,
                $order++,
                '',
                $content
            );
        }
    }

    $pdo->commit();
    echo "Seed complet: categorii și prețuri importate.\n";
    if ($countOffers === 0) {
        echo "Oferte homepage: " . count($defaults ?? []) . " slide-uri create.\n";
    }
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, 'Eroare seed: ' . $e->getMessage() . "\n");
    exit(1);
}
