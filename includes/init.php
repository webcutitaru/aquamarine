<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/** @var array<string, mixed> $config */
$config = require __DIR__ . '/config.php';

require_once __DIR__ . '/i18n.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/repository/pricing.php';
require_once __DIR__ . '/repository/offers.php';
require_once __DIR__ . '/repository/leads.php';
require_once __DIR__ . '/admin/csrf.php';

function aquamarine_production_base_url(array $config): string
{
    $u = isset($config['production_base_url']) ? trim((string) $config['production_base_url']) : 'https://aquamarine.md';

    return rtrim($u, '/');
}

/**
 * Web path prefix when the app lives in a subdirectory (e.g. /aquamarine on MAMP).
 */
function aquamarine_app_root(): string
{
    static $root = null;
    if ($root !== null) {
        return $root;
    }

    $script = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
    $dir = str_replace('\\', '/', dirname($script));
    if (preg_match('#/ru$#', $dir) === 1) {
        $dir = dirname($dir);
    }
    if ($dir === '/' || $dir === '.' || $dir === '') {
        $root = '';
    } else {
        $root = rtrim($dir, '/');
    }

    return $root;
}

function aquamarine_asset_url(string $path): string
{
    return aquamarine_app_root() . '/assets/' . ltrim($path, '/');
}

function aquamarine_is_production_host(array $config): bool
{
    $prodHost = parse_url(aquamarine_production_base_url($config), PHP_URL_HOST);
    if (! is_string($prodHost) || $prodHost === '') {
        return false;
    }

    $current = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $currentHost = preg_replace('/:\d+$/', '', $current) ?? $current;

    return strtolower($prodHost) === $currentHost;
}

/**
 * Absolute base URL for the current request (localhost, staging, or production).
 */
function aquamarine_request_base_url(array $config): string
{
    $https = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
    $scheme = $https ? 'https' : 'http';
    $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');

    return $scheme . '://' . $host . aquamarine_app_root();
}

/**
 * Base URL for SEO tags: production URL on live host, current request otherwise.
 */
function aquamarine_seo_base_url(array $config): string
{
    if (aquamarine_is_production_host($config)) {
        return aquamarine_production_base_url($config);
    }

    return aquamarine_request_base_url($config);
}

function aquamarine_is_staging(array $config): bool
{
    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $patterns = $config['staging_host_contains'] ?? [];
    if (! is_array($patterns)) {
        return false;
    }
    foreach ($patterns as $p) {
        $p = strtolower(trim((string) $p));
        if ($p !== '' && str_contains($host, $p)) {
            return true;
        }
    }

    return false;
}

function aquamarine_canonical_path(): string
{
    $path = aquamarine_path_without_locale();
    if ($path === '/') {
        return '/';
    }

    return $path;
}

/**
 * Absolute URL for the same page in another locale.
 */
function aquamarine_locale_url(string $locale, array $config): string
{
    $base = aquamarine_request_base_url($config);
    $path = aquamarine_path_without_locale();
    if ($path === '/' || $path === '/index.php') {
        return $locale === 'ru' ? $base . '/ru/' : $base . '/';
    }
    $clean = ltrim($path, '/');
    if ($locale === 'ru') {
        return $base . '/ru/' . $clean;
    }

    return $base . '/' . $clean;
}

/**
 * @return list<array<string, mixed>>
 */
function aquamarine_opening_hours_spec(array $config): array
{
    $spec = $config['opening_hours_spec'] ?? null;

    return is_array($spec) ? $spec : [];
}

/**
 * @return array<string, mixed>|null
 */
function aquamarine_location_by_city(array $config, string $city): ?array
{
    foreach ($config['locations'] ?? [] as $loc) {
        if (! is_array($loc)) {
            continue;
        }
        if (($loc['city'] ?? '') === $city) {
            return $loc;
        }
    }

    return null;
}

/**
 * @param array<string, mixed>|null $loc
 * @return list<array<string, mixed>>
 */
function aquamarine_opening_hours_for_location(?array $loc, array $config): array
{
    if ($loc !== null && isset($loc['opening_hours_spec']) && is_array($loc['opening_hours_spec'])) {
        return $loc['opening_hours_spec'];
    }

    return aquamarine_opening_hours_spec($config);
}

/**
 * Rânduri pentru afișare UI (etichetă + interval), din același format ca schema.org OpeningHoursSpecification.
 *
 * @param list<array<string, mixed>> $spec
 * @return list<array{0:string, 1:string}>
 */
function aquamarine_opening_hours_ui_rows(array $spec): array
{
    $weekdays = null;
    $satRange = null;
    $sunRange = null;
    foreach ($spec as $row) {
        if (! is_array($row)) {
            continue;
        }
        $opens = (string) ($row['opens'] ?? '');
        $closes = (string) ($row['closes'] ?? '');
        if ($opens === '' || $closes === '') {
            continue;
        }
        $range = $opens . ' – ' . $closes;
        $dow = $row['dayOfWeek'] ?? null;
        if (is_array($dow)) {
            $weekdays = [t('days.weekdays'), $range];
        } elseif ($dow === 'Saturday') {
            $satRange = $range;
        } elseif ($dow === 'Sunday') {
            $sunRange = $range;
        }
    }

    $out = [];
    if ($weekdays !== null) {
        $out[] = $weekdays;
    }
    if ($satRange !== null && $sunRange !== null && $satRange === $sunRange) {
        $out[] = [t('days.sat_sun'), $satRange];
    } else {
        if ($satRange !== null) {
            $out[] = [t('days.saturday'), $satRange];
        }
        if ($sunRange !== null) {
            $out[] = [t('days.sunday'), $sunRange];
        }
    }

    return $out;
}

function esc(string|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * @return array<string, string|null>
 */
function flash_pull(string $key): array
{
    if (! isset($_SESSION['flash'][$key]) || ! is_array($_SESSION['flash'][$key])) {
        return ['type' => null, 'message' => null];
    }

    $data = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    $type = isset($data['type']) ? (string) $data['type'] : null;
    $msg = isset($data['message']) ? (string) $data['message'] : null;

    return ['type' => $type, 'message' => $msg];
}

function flash_set(string $key, string $type, string $message): void
{
    $_SESSION['flash'][$key] = ['type' => $type, 'message' => $message];
}
