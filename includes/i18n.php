<?php

declare(strict_types=1);

/** @var list<string> */
$GLOBALS['_aquamarine_i18n_loaded'] = $GLOBALS['_aquamarine_i18n_loaded'] ?? [];

/** @var array<string, string> */
$GLOBALS['_aquamarine_i18n_strings'] = $GLOBALS['_aquamarine_i18n_strings'] ?? [];

/** @var array<string, string> */
$GLOBALS['_aquamarine_i18n_fallback'] = $GLOBALS['_aquamarine_i18n_fallback'] ?? [];

/** Page-specific lang module (e.g. home, contact). Set before header. */
$GLOBALS['_aquamarine_page_lang'] = $GLOBALS['_aquamarine_page_lang'] ?? null;

function aquamarine_detect_locale(): string
{
    $env = $_SERVER['REDIRECT_AQUAMARINE_LOCALE'] ?? $_SERVER['AQUAMARINE_LOCALE'] ?? '';
    if (is_string($env) && strtolower($env) === 'ru') {
        return 'ru';
    }

    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $path = parse_url($uri, PHP_URL_PATH);
    if (! is_string($path)) {
        return 'ro';
    }

    $appRoot = aquamarine_app_root();
    if ($appRoot !== '' && str_starts_with($path, $appRoot)) {
        $path = substr($path, strlen($appRoot)) ?: '/';
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }
    }

    if (preg_match('#^/ru(/|$)#', $path) === 1) {
        return 'ru';
    }

    return 'ro';
}

function aquamarine_locale(): string
{
    static $locale = null;
    if ($locale === null) {
        $locale = aquamarine_detect_locale();
        if ($locale !== 'ru') {
            $locale = 'ro';
        }
    }

    return $locale;
}

function aquamarine_set_page_lang(?string $module): void
{
    $GLOBALS['_aquamarine_page_lang'] = $module;
    if ($module !== null && $module !== '') {
        aquamarine_load_lang_file('ro', $module);
        if (aquamarine_locale() === 'ru') {
            aquamarine_load_lang_file('ru', $module);
        }
    }
}

function aquamarine_load_lang_file(string $locale, string $module): void
{
    $key = $locale . ':' . $module;
    $loaded = &$GLOBALS['_aquamarine_i18n_loaded'];
    if (in_array($key, $loaded, true)) {
        return;
    }

    $path = dirname(__DIR__) . '/lang/' . $locale . '/' . $module . '.php';
    if (! is_readable($path)) {
        $loaded[] = $key;

        return;
    }

    /** @var array<string, string>|mixed $strings */
    $strings = require $path;
    if (! is_array($strings)) {
        $loaded[] = $key;

        return;
    }

    foreach ($strings as $k => $v) {
        if (! is_string($k) || (! is_string($v) && ! is_numeric($v))) {
            continue;
        }
        if ($locale === 'ro') {
            $GLOBALS['_aquamarine_i18n_fallback'][$k] = (string) $v;
        } else {
            $GLOBALS['_aquamarine_i18n_strings'][$k] = (string) $v;
        }
    }

    $loaded[] = $key;
}

function aquamarine_i18n_bootstrap(): void
{
    aquamarine_load_lang_file('ro', 'common');
    if (aquamarine_locale() === 'ru') {
        aquamarine_load_lang_file('ru', 'common');
    }

    $page = $GLOBALS['_aquamarine_page_lang'] ?? null;
    if (is_string($page) && $page !== '') {
        aquamarine_set_page_lang($page);
    }
}

/**
 * @param array<string, string|int|float> $replace
 */
function t(string $key, array $replace = []): string
{
    static $booted = false;
    if (! $booted) {
        aquamarine_i18n_bootstrap();
        $booted = true;
    }

    $strings = &$GLOBALS['_aquamarine_i18n_strings'];
    $fallback = &$GLOBALS['_aquamarine_i18n_fallback'];

    $text = $strings[$key] ?? $fallback[$key] ?? $key;

    foreach ($replace as $name => $value) {
        $text = str_replace(':' . $name, (string) $value, $text);
    }

    return $text;
}

function aquamarine_path_without_locale(?string $path = null): string
{
    if ($path === null) {
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH);
    }
    if (! is_string($path) || $path === '') {
        return '/';
    }
    if ($path[0] !== '/') {
        $path = '/' . $path;
    }

    $appRoot = aquamarine_app_root();
    if ($appRoot !== '' && str_starts_with($path, $appRoot)) {
        $path = substr($path, strlen($appRoot));
        if ($path === '' || $path === false) {
            $path = '/';
        }
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }
    }

    if (preg_match('#^/ru(/.*)?$#', $path, $m) === 1) {
        $rest = $m[1] ?? '';
        if ($rest === '' || $rest === '/') {
            return '/';
        }

        return $rest;
    }

    return $path;
}

function aquamarine_url(string $path = ''): string
{
    $root = aquamarine_app_root();
    $path = ltrim($path, '/');
    if ($path === '' || $path === 'index.php') {
        $suffix = aquamarine_locale() === 'ru' ? '/ru/' : '/';

        return $root . $suffix;
    }

    if (aquamarine_locale() === 'ru') {
        return $root . '/ru/' . $path;
    }

    return $root . '/' . $path;
}

/**
 * City display label for narrative UI (config keys stay RO).
 */
function aquamarine_city_label(string $cityRo): string
{
    $map = [
        'Bălți' => t('city.balti'),
        'Edineț' => t('city.edinet'),
        'Briceni' => t('city.briceni'),
        'Drochia' => t('city.drochia'),
    ];

    return $map[$cityRo] ?? $cityRo;
}

/**
 * @return list<string>
 */
function aquamarine_cities_line(): array
{
    return [
        aquamarine_city_label('Bălți'),
        aquamarine_city_label('Edineț'),
        aquamarine_city_label('Briceni'),
        aquamarine_city_label('Drochia'),
    ];
}

function aquamarine_cities_badge(): string
{
    return implode(' · ', aquamarine_cities_line());
}

/**
 * Structured page copy (lists, FAQ items, etc.).
 *
 * @return array<string, mixed>
 */
function lang_data(string $module): array
{
    $locale = aquamarine_locale();
    $base = dirname(__DIR__) . '/lang';
    $path = $base . '/' . $locale . '/' . $module . '_data.php';
    if (! is_readable($path)) {
        $path = $base . '/ro/' . $module . '_data.php';
    }
    if (! is_readable($path)) {
        return [];
    }

    $data = require $path;

    return is_array($data) ? $data : [];
}
