<?php

declare(strict_types=1);

function pricing_locale_field(string $field, array $row, string $locale): string
{
    if ($locale === 'ru') {
        $ruKey = $field . '_ru';
        $ru = isset($row[$ruKey]) ? trim((string) $row[$ruKey]) : '';
        if ($ru !== '') {
            return $ru;
        }
    }

    return (string) ($row[$field] ?? '');
}

/**
 * @return array{note: string, currency: string, categories: list<array<string, mixed>>}
 */
function pricing_fetch_catalog(?PDO $pdo, ?string $locale = null): array
{
    $locale ??= aquamarine_locale();

    if (! $pdo instanceof PDO) {
        error_log('Aquamarine: pricing catalog requested without DB connection');

        return ['note' => '', 'currency' => 'MDL', 'categories' => []];
    }

    $hasRu = pricing_db_has_ru_columns($pdo);

    $settingsSql = $hasRu && $locale === 'ru'
        ? 'SELECT note, note_ru, currency FROM price_settings WHERE id = 1'
        : 'SELECT note, currency FROM price_settings WHERE id = 1';
    $settings = $pdo->query($settingsSql)->fetch();
    $note = '';
    if (is_array($settings)) {
        $note = $hasRu && $locale === 'ru'
            ? pricing_locale_field('note', $settings, $locale)
            : (string) ($settings['note'] ?? '');
    }
    $currency = is_array($settings) ? (string) ($settings['currency'] ?? 'MDL') : 'MDL';
    if ($currency === '') {
        $currency = 'MDL';
    }

    $catSql = $hasRu
        ? 'SELECT id, name, name_ru, footnote, footnote_ru, sort_order FROM price_categories ORDER BY sort_order ASC, id ASC'
        : 'SELECT id, name, footnote, sort_order FROM price_categories ORDER BY sort_order ASC, id ASC';
    $catStmt = $pdo->query($catSql);
    $categories = [];

    $itemSql = $hasRu
        ? 'SELECT service, service_ru, price, description, description_ru, note, note_ru FROM price_items WHERE category_id = ? ORDER BY sort_order ASC, id ASC'
        : 'SELECT service, price, description, note FROM price_items WHERE category_id = ? ORDER BY sort_order ASC, id ASC';
    $itemStmt = $pdo->prepare($itemSql);

    while ($cat = $catStmt->fetch()) {
        if (! is_array($cat)) {
            continue;
        }
        $catId = (int) $cat['id'];
        $itemStmt->execute([$catId]);
        $items = [];
        while ($row = $itemStmt->fetch()) {
            if (! is_array($row)) {
                continue;
            }
            $item = [
                'service' => $hasRu ? pricing_locale_field('service', $row, $locale) : (string) $row['service'],
                'price' => (string) $row['price'],
                'description' => $hasRu ? pricing_locale_field('description', $row, $locale) : (string) ($row['description'] ?? ''),
            ];
            $itemNote = $hasRu ? pricing_locale_field('note', $row, $locale) : (string) ($row['note'] ?? '');
            if ($itemNote !== '') {
                $item['note'] = $itemNote;
            }
            $items[] = $item;
        }

        $entry = [
            'name' => $hasRu ? pricing_locale_field('name', $cat, $locale) : (string) $cat['name'],
            'items' => $items,
        ];
        $footnote = $hasRu ? pricing_locale_field('footnote', $cat, $locale) : (string) ($cat['footnote'] ?? '');
        if ($footnote !== '') {
            $entry['footnote'] = $footnote;
        }
        $categories[] = $entry;
    }

    return ['note' => $note, 'currency' => $currency, 'categories' => $categories];
}

function pricing_db_has_ru_columns(PDO $pdo): bool
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM price_categories LIKE 'name_ru'");
        $cache = $stmt !== false && $stmt->fetch() !== false;
    } catch (Throwable) {
        $cache = false;
    }

    return $cache;
}

/**
 * @return array{note: string, currency: string, categories: list<array<string, mixed>>}
 */
function pricing_fetch_catalog_from_json(?string $locale = null): array
{
    $locale ??= aquamarine_locale();
    $file = $locale === 'ru' ? 'preturi.ru.json' : 'preturi.json';
    $pathJson = dirname(__DIR__, 2) . '/data/' . $file;
    if (! is_readable($pathJson) && $locale === 'ru') {
        $pathJson = dirname(__DIR__, 2) . '/data/preturi.json';
    }
    $decoded = [];
    if (is_readable($pathJson)) {
        $raw = file_get_contents($pathJson);
        $decoded = is_string($raw) ? json_decode($raw, true) : [];
    }
    if (! is_array($decoded)) {
        $decoded = [];
    }

    return [
        'note' => isset($decoded['note']) ? (string) $decoded['note'] : '',
        'currency' => isset($decoded['currency']) ? (string) $decoded['currency'] : 'MDL',
        'categories' => isset($decoded['categories']) && is_array($decoded['categories']) ? $decoded['categories'] : [],
    ];
}

function pricing_save_settings(PDO $pdo, string $note, string $currency, ?string $noteRu = null): void
{
    if (pricing_db_has_ru_columns($pdo)) {
        $stmt = $pdo->prepare('UPDATE price_settings SET note = ?, note_ru = ?, currency = ? WHERE id = 1');
        $stmt->execute([
            $note !== '' ? $note : null,
            $noteRu !== null && $noteRu !== '' ? $noteRu : null,
            $currency !== '' ? $currency : 'MDL',
        ]);

        return;
    }

    $stmt = $pdo->prepare('UPDATE price_settings SET note = ?, currency = ? WHERE id = 1');
    $stmt->execute([$note !== '' ? $note : null, $currency !== '' ? $currency : 'MDL']);
}

/**
 * @return list<array<string, mixed>>
 */
function pricing_fetch_categories_admin(PDO $pdo): array
{
    $sql = pricing_db_has_ru_columns($pdo)
        ? 'SELECT id, name, name_ru, footnote, footnote_ru, sort_order FROM price_categories ORDER BY sort_order ASC, id ASC'
        : 'SELECT id, name, footnote, sort_order FROM price_categories ORDER BY sort_order ASC, id ASC';
    $stmt = $pdo->query($sql);

    return $stmt->fetchAll() ?: [];
}

/**
 * @return list<array<string, mixed>>
 */
function pricing_fetch_items_by_category(PDO $pdo, int $categoryId): array
{
    $sql = pricing_db_has_ru_columns($pdo)
        ? 'SELECT id, service, service_ru, price, description, description_ru, note, note_ru, sort_order FROM price_items WHERE category_id = ? ORDER BY sort_order ASC, id ASC'
        : 'SELECT id, service, price, description, note, sort_order FROM price_items WHERE category_id = ? ORDER BY sort_order ASC, id ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$categoryId]);

    return $stmt->fetchAll() ?: [];
}

function pricing_create_category(PDO $pdo, string $name, ?string $footnote, int $sortOrder, ?string $nameRu = null, ?string $footnoteRu = null): int
{
    if (pricing_db_has_ru_columns($pdo)) {
        $stmt = $pdo->prepare(
            'INSERT INTO price_categories (name, name_ru, footnote, footnote_ru, sort_order) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $name,
            $nameRu !== null && $nameRu !== '' ? $nameRu : null,
            $footnote !== '' ? $footnote : null,
            $footnoteRu !== null && $footnoteRu !== '' ? $footnoteRu : null,
            $sortOrder,
        ]);
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO price_categories (name, footnote, sort_order) VALUES (?, ?, ?)'
        );
        $stmt->execute([$name, $footnote !== '' ? $footnote : null, $sortOrder]);
    }

    return (int) $pdo->lastInsertId();
}

function pricing_update_category(
    PDO $pdo,
    int $id,
    string $name,
    ?string $footnote,
    int $sortOrder,
    ?string $nameRu = null,
    ?string $footnoteRu = null
): void {
    if (pricing_db_has_ru_columns($pdo)) {
        $stmt = $pdo->prepare(
            'UPDATE price_categories SET name = ?, name_ru = ?, footnote = ?, footnote_ru = ?, sort_order = ? WHERE id = ?'
        );
        $stmt->execute([
            $name,
            $nameRu !== null && $nameRu !== '' ? $nameRu : null,
            $footnote !== '' ? $footnote : null,
            $footnoteRu !== null && $footnoteRu !== '' ? $footnoteRu : null,
            $sortOrder,
            $id,
        ]);

        return;
    }

    $stmt = $pdo->prepare(
        'UPDATE price_categories SET name = ?, footnote = ?, sort_order = ? WHERE id = ?'
    );
    $stmt->execute([$name, $footnote !== '' ? $footnote : null, $sortOrder, $id]);
}

function pricing_delete_category(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM price_categories WHERE id = ?');
    $stmt->execute([$id]);
}

function pricing_create_item(
    PDO $pdo,
    int $categoryId,
    string $service,
    string $price,
    ?string $description,
    ?string $note,
    int $sortOrder,
    ?string $serviceRu = null,
    ?string $descriptionRu = null,
    ?string $noteRu = null
): int {
    if (pricing_db_has_ru_columns($pdo)) {
        $stmt = $pdo->prepare(
            'INSERT INTO price_items (category_id, service, service_ru, price, description, description_ru, note, note_ru, sort_order)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $categoryId,
            $service,
            $serviceRu !== null && $serviceRu !== '' ? $serviceRu : null,
            $price,
            $description !== '' ? $description : null,
            $descriptionRu !== null && $descriptionRu !== '' ? $descriptionRu : null,
            $note !== '' ? $note : null,
            $noteRu !== null && $noteRu !== '' ? $noteRu : null,
            $sortOrder,
        ]);
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO price_items (category_id, service, price, description, note, sort_order)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $categoryId,
            $service,
            $price,
            $description !== '' ? $description : null,
            $note !== '' ? $note : null,
            $sortOrder,
        ]);
    }

    return (int) $pdo->lastInsertId();
}

function pricing_update_item(
    PDO $pdo,
    int $id,
    string $service,
    string $price,
    ?string $description,
    ?string $note,
    int $sortOrder,
    ?string $serviceRu = null,
    ?string $descriptionRu = null,
    ?string $noteRu = null
): void {
    if (pricing_db_has_ru_columns($pdo)) {
        $stmt = $pdo->prepare(
            'UPDATE price_items SET service = ?, service_ru = ?, price = ?, description = ?, description_ru = ?, note = ?, note_ru = ?, sort_order = ? WHERE id = ?'
        );
        $stmt->execute([
            $service,
            $serviceRu !== null && $serviceRu !== '' ? $serviceRu : null,
            $price,
            $description !== '' ? $description : null,
            $descriptionRu !== null && $descriptionRu !== '' ? $descriptionRu : null,
            $note !== '' ? $note : null,
            $noteRu !== null && $noteRu !== '' ? $noteRu : null,
            $sortOrder,
            $id,
        ]);

        return;
    }

    $stmt = $pdo->prepare(
        'UPDATE price_items SET service = ?, price = ?, description = ?, note = ?, sort_order = ? WHERE id = ?'
    );
    $stmt->execute([
        $service,
        $price,
        $description !== '' ? $description : null,
        $note !== '' ? $note : null,
        $sortOrder,
        $id,
    ]);
}

function pricing_delete_item(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM price_items WHERE id = ?');
    $stmt->execute([$id]);
}
