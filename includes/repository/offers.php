<?php

declare(strict_types=1);

/**
 * @return list<array{image: string, alt: string, href: string, eyebrow: string, heading: string, sub: string}>
 */
function offers_fetch_active_slides(?PDO $pdo): array
{
    if (! $pdo instanceof PDO) {
        return offers_default_slides();
    }

    $hasContent = offers_db_has_slide_content($pdo);
    $isRu = aquamarine_locale() === 'ru';

    if ($hasContent) {
        $altCol = $isRu ? 'COALESCE(NULLIF(alt_ru, \'\'), alt)' : 'alt';
        $eyebrowCol = $isRu ? 'COALESCE(NULLIF(eyebrow_ru, \'\'), eyebrow)' : 'eyebrow';
        $headingCol = $isRu ? 'COALESCE(NULLIF(heading_ru, \'\'), heading)' : 'heading';
        $subCol = $isRu ? 'COALESCE(NULLIF(sub_ru, \'\'), sub)' : 'sub';
        $stmt = $pdo->query(
            "SELECT image_path, {$altCol} AS alt, href,
                    {$eyebrowCol} AS eyebrow, {$headingCol} AS heading, {$subCol} AS sub
             FROM homepage_offers
             WHERE is_active = 1 ORDER BY sort_order ASC, id ASC"
        );
    } else {
        $hasAltRu = offers_db_has_alt_ru($pdo);
        $altCol = $hasAltRu && $isRu ? 'COALESCE(NULLIF(alt_ru, \'\'), alt)' : 'alt';
        $stmt = $pdo->query(
            "SELECT image_path, {$altCol} AS alt, href FROM homepage_offers
             WHERE is_active = 1 ORDER BY sort_order ASC, id ASC"
        );
    }

    $langFallback = offers_lang_overlay_defaults();
    $slides = [];
    while ($row = $stmt->fetch()) {
        if (! is_array($row)) {
            continue;
        }
        $img = (string) ($row['image_path'] ?? '');
        if ($img === '') {
            continue;
        }
        $slides[] = [
            'image' => offers_public_image_url($img),
            'alt' => (string) ($row['alt'] ?? ''),
            'href' => (string) ($row['href'] ?? ''),
            'eyebrow' => offers_resolve_slide_field($row, 'eyebrow', $langFallback['eyebrow']),
            'heading' => offers_resolve_slide_field($row, 'heading', $langFallback['heading']),
            'sub' => offers_resolve_slide_field($row, 'sub', $langFallback['sub']),
        ];
    }

    if ($slides === []) {
        return offers_default_slides();
    }

    return $slides;
}

/**
 * @param array<string, mixed> $row
 */
function offers_resolve_slide_field(array $row, string $key, string $fallback): string
{
    $val = trim((string) ($row[$key] ?? ''));

    return $val !== '' ? $val : $fallback;
}

/**
 * @return array{eyebrow: string, heading: string, sub: string}
 */
function offers_lang_overlay_defaults(): array
{
    $offers = lang_data('home')['offers'] ?? [];
    if (! is_array($offers)) {
        $offers = [];
    }

    $line1 = trim((string) ($offers['heading_line1'] ?? ''));
    $line2 = trim((string) ($offers['heading_line2'] ?? ''));
    $heading = trim((string) ($offers['heading'] ?? ''));
    if ($heading === '' && ($line1 !== '' || $line2 !== '')) {
        $heading = $line1 . '|' . $line2;
    }

    return [
        'eyebrow' => 'Aquamarine',
        'heading' => $heading,
        'sub' => (string) ($offers['sub'] ?? ''),
    ];
}

function offers_db_has_alt_ru(PDO $pdo): bool
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM homepage_offers LIKE 'alt_ru'");
        $cache = $stmt !== false && $stmt->fetch() !== false;
    } catch (Throwable) {
        $cache = false;
    }

    return $cache;
}

function offers_db_has_slide_content(PDO $pdo): bool
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM homepage_offers LIKE 'heading'");
        $cache = $stmt !== false && $stmt->fetch() !== false;
    } catch (Throwable) {
        $cache = false;
    }

    return $cache;
}

function offers_public_image_url(string $imagePath): string
{
    if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
        return $imagePath;
    }

    $rel = ltrim($imagePath, '/');
    if (str_starts_with($rel, 'assets/')) {
        return aquamarine_asset_url(substr($rel, strlen('assets/')));
    }

    return aquamarine_app_root() . '/' . $rel;
}

/**
 * @return list<array{image: string, alt: string, href: string, eyebrow: string, heading: string, sub: string}>
 */
function offers_default_slides(): array
{
    $offers = lang_data('home')['offers'] ?? [];
    if (! is_array($offers)) {
        $offers = [];
    }

    $overlay = offers_lang_overlay_defaults();

    return [
        [
            'image' => offers_public_image_url('assets/images/oferte/aquamarine_cleaning_system.webp'),
            'alt' => (string) ($offers['slide1_alt'] ?? ''),
            'href' => '',
            'eyebrow' => $overlay['eyebrow'],
            'heading' => $overlay['heading'],
            'sub' => $overlay['sub'],
        ],
        [
            'image' => offers_public_image_url('assets/images/oferte/aquamarine_cleaning_system_slider.webp'),
            'alt' => (string) ($offers['slide2_alt'] ?? ''),
            'href' => '',
            'eyebrow' => $overlay['eyebrow'],
            'heading' => $overlay['heading'],
            'sub' => $overlay['sub'],
        ],
    ];
}

/**
 * @return array{eyebrow: string, eyebrow_ru: string, heading: string, heading_ru: string, sub: string, sub_ru: string}
 */
function offers_default_content_seed(): array
{
    $ro = require dirname(__DIR__, 2) . '/lang/ro/home_data.php';
    $ru = require dirname(__DIR__, 2) . '/lang/ru/home_data.php';
    $roOffers = is_array($ro['offers'] ?? null) ? $ro['offers'] : [];
    $ruOffers = is_array($ru['offers'] ?? null) ? $ru['offers'] : [];

    return [
        'eyebrow' => 'Aquamarine',
        'eyebrow_ru' => 'Aquamarine',
        'heading' => (string) ($roOffers['heading'] ?? ''),
        'heading_ru' => (string) ($ruOffers['heading'] ?? ''),
        'sub' => (string) ($roOffers['sub'] ?? ''),
        'sub_ru' => (string) ($ruOffers['sub'] ?? ''),
    ];
}

/**
 * @return list<array<string, mixed>>
 */
function offers_fetch_all_admin(PDO $pdo): array
{
    $cols = 'id, image_path, alt, href, is_active, sort_order, created_at';
    if (offers_db_has_alt_ru($pdo)) {
        $cols = 'id, image_path, alt, alt_ru, href, is_active, sort_order, created_at';
    }
    if (offers_db_has_slide_content($pdo)) {
        $cols = 'id, image_path, alt, alt_ru, eyebrow, eyebrow_ru, heading, heading_ru, sub, sub_ru, href, is_active, sort_order, created_at';
    }
    $stmt = $pdo->query("SELECT {$cols} FROM homepage_offers ORDER BY sort_order ASC, id ASC");

    return $stmt->fetchAll() ?: [];
}

function offers_find_by_id(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM homepage_offers WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    return is_array($row) ? $row : null;
}

/**
 * @param array{eyebrow?: string, eyebrow_ru?: string, heading?: string, heading_ru?: string, sub?: string, sub_ru?: string} $content
 */
function offers_create(
    PDO $pdo,
    string $imagePath,
    string $alt,
    string $href,
    bool $active,
    int $sortOrder,
    string $altRu = '',
    array $content = []
): int {
    $eyebrow = trim((string) ($content['eyebrow'] ?? ''));
    $eyebrowRu = trim((string) ($content['eyebrow_ru'] ?? ''));
    $heading = trim((string) ($content['heading'] ?? ''));
    $headingRu = trim((string) ($content['heading_ru'] ?? ''));
    $sub = trim((string) ($content['sub'] ?? ''));
    $subRu = trim((string) ($content['sub_ru'] ?? ''));

    if (offers_db_has_slide_content($pdo)) {
        $stmt = $pdo->prepare(
            'INSERT INTO homepage_offers (
                image_path, alt, alt_ru, eyebrow, eyebrow_ru, heading, heading_ru, sub, sub_ru, href, is_active, sort_order
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $imagePath, $alt, $altRu, $eyebrow, $eyebrowRu, $heading, $headingRu,
            $sub !== '' ? $sub : null, $subRu !== '' ? $subRu : null,
            $href, $active ? 1 : 0, $sortOrder,
        ]);
    } elseif (offers_db_has_alt_ru($pdo)) {
        $stmt = $pdo->prepare(
            'INSERT INTO homepage_offers (image_path, alt, alt_ru, href, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$imagePath, $alt, $altRu, $href, $active ? 1 : 0, $sortOrder]);
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO homepage_offers (image_path, alt, href, is_active, sort_order) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$imagePath, $alt, $href, $active ? 1 : 0, $sortOrder]);
    }

    return (int) $pdo->lastInsertId();
}

/**
 * @param array{eyebrow?: string, eyebrow_ru?: string, heading?: string, heading_ru?: string, sub?: string, sub_ru?: string} $content
 */
function offers_update(
    PDO $pdo,
    int $id,
    string $alt,
    string $href,
    bool $active,
    int $sortOrder,
    ?string $imagePath = null,
    string $altRu = '',
    array $content = []
): void {
    $eyebrow = trim((string) ($content['eyebrow'] ?? ''));
    $eyebrowRu = trim((string) ($content['eyebrow_ru'] ?? ''));
    $heading = trim((string) ($content['heading'] ?? ''));
    $headingRu = trim((string) ($content['heading_ru'] ?? ''));
    $sub = trim((string) ($content['sub'] ?? ''));
    $subRu = trim((string) ($content['sub_ru'] ?? ''));
    $subVal = $sub !== '' ? $sub : null;
    $subRuVal = $subRu !== '' ? $subRu : null;

    $hasContent = offers_db_has_slide_content($pdo);
    $hasAltRu = offers_db_has_alt_ru($pdo);

    if ($imagePath !== null && $imagePath !== '') {
        if ($hasContent) {
            $stmt = $pdo->prepare(
                'UPDATE homepage_offers SET image_path = ?, alt = ?, alt_ru = ?, eyebrow = ?, eyebrow_ru = ?,
                    heading = ?, heading_ru = ?, sub = ?, sub_ru = ?, href = ?, is_active = ?, sort_order = ? WHERE id = ?'
            );
            $stmt->execute([
                $imagePath, $alt, $altRu, $eyebrow, $eyebrowRu, $heading, $headingRu,
                $subVal, $subRuVal, $href, $active ? 1 : 0, $sortOrder, $id,
            ]);
        } elseif ($hasAltRu) {
            $stmt = $pdo->prepare(
                'UPDATE homepage_offers SET image_path = ?, alt = ?, alt_ru = ?, href = ?, is_active = ?, sort_order = ? WHERE id = ?'
            );
            $stmt->execute([$imagePath, $alt, $altRu, $href, $active ? 1 : 0, $sortOrder, $id]);
        } else {
            $stmt = $pdo->prepare(
                'UPDATE homepage_offers SET image_path = ?, alt = ?, href = ?, is_active = ?, sort_order = ? WHERE id = ?'
            );
            $stmt->execute([$imagePath, $alt, $href, $active ? 1 : 0, $sortOrder, $id]);
        }
    } elseif ($hasContent) {
        $stmt = $pdo->prepare(
            'UPDATE homepage_offers SET alt = ?, alt_ru = ?, eyebrow = ?, eyebrow_ru = ?, heading = ?, heading_ru = ?,
                sub = ?, sub_ru = ?, href = ?, is_active = ?, sort_order = ? WHERE id = ?'
        );
        $stmt->execute([
            $alt, $altRu, $eyebrow, $eyebrowRu, $heading, $headingRu,
            $subVal, $subRuVal, $href, $active ? 1 : 0, $sortOrder, $id,
        ]);
    } elseif ($hasAltRu) {
        $stmt = $pdo->prepare(
            'UPDATE homepage_offers SET alt = ?, alt_ru = ?, href = ?, is_active = ?, sort_order = ? WHERE id = ?'
        );
        $stmt->execute([$alt, $altRu, $href, $active ? 1 : 0, $sortOrder, $id]);
    } else {
        $stmt = $pdo->prepare(
            'UPDATE homepage_offers SET alt = ?, href = ?, is_active = ?, sort_order = ? WHERE id = ?'
        );
        $stmt->execute([$alt, $href, $active ? 1 : 0, $sortOrder, $id]);
    }
}

function offers_delete(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM homepage_offers WHERE id = ?');
    $stmt->execute([$id]);
}

/**
 * @return array{eyebrow: string, eyebrow_ru: string, heading: string, heading_ru: string, sub: string, sub_ru: string}
 */
function offers_content_from_post(): array
{
    return [
        'eyebrow' => trim((string) ($_POST['eyebrow'] ?? '')),
        'eyebrow_ru' => trim((string) ($_POST['eyebrow_ru'] ?? '')),
        'heading' => trim((string) ($_POST['heading'] ?? '')),
        'heading_ru' => trim((string) ($_POST['heading_ru'] ?? '')),
        'sub' => trim((string) ($_POST['sub'] ?? '')),
        'sub_ru' => trim((string) ($_POST['sub_ru'] ?? '')),
    ];
}
