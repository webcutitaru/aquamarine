<?php

declare(strict_types=1);

/**
 * Parser simplu .env — fallback când lipsește Composer sau dotenv nu populează variabilele.
 */
function aquamarine_parse_env_file(string $path): void
{
    $raw = @file_get_contents($path);
    if ($raw === false) {
        return;
    }

    foreach (preg_split('/\R/', $raw) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (! str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        if ($key === '') {
            continue;
        }

        $value = trim($value);
        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"'))
            || (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        } elseif (str_contains($value, '#')) {
            $value = trim(preg_replace('/\s+#.*$/', '', $value) ?? $value);
        }

        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        if (function_exists('putenv')) {
            @putenv($key . '=' . $value);
        }
    }
}

function aquamarine_find_env_file(): ?string
{
    $root = dirname(__DIR__);
    $candidates = [];
    $explicit = getenv('AQUAMARINE_ENV_PATH');
    if (is_string($explicit) && $explicit !== '') {
        $candidates[] = rtrim($explicit, '/');
    }
    $candidates[] = $root;
    $candidates[] = dirname($root);

    foreach ($candidates as $dir) {
        if (! is_dir($dir)) {
            continue;
        }
        $envFile = $dir . '/.env';
        if (is_readable($envFile)) {
            return $envFile;
        }
    }

    return null;
}

function aquamarine_load_env(): void
{
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $loaded = true;

    $envFile = aquamarine_find_env_file();
    if ($envFile === null) {
        return;
    }

    $dir = dirname($envFile);
    $autoload = dirname(__DIR__) . '/vendor/autoload.php';
    if (is_readable($autoload)) {
        require_once $autoload;
        Dotenv\Dotenv::createMutable($dir)->safeLoad();
    }

    if (aquamarine_env('DB_NAME') === null || aquamarine_env('DB_USER') === null) {
        aquamarine_parse_env_file($envFile);
    }
}

function aquamarine_env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    if ($value === false && isset($_ENV[$key])) {
        $value = $_ENV[$key];
    }
    if ($value === false && isset($_SERVER[$key])) {
        $value = $_SERVER[$key];
    }
    if ($value === false || $value === '') {
        return $default;
    }

    return is_string($value) ? $value : (string) $value;
}
