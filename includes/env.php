<?php

declare(strict_types=1);

function aquamarine_load_env(): void
{
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $loaded = true;

    $autoload = dirname(__DIR__) . '/vendor/autoload.php';
    if (! is_readable($autoload)) {
        return;
    }

    require_once $autoload;

    $candidates = [];
    $explicit = getenv('AQUAMARINE_ENV_PATH');
    if (is_string($explicit) && $explicit !== '') {
        $candidates[] = rtrim($explicit, '/');
    }
    $candidates[] = dirname(__DIR__);
    $candidates[] = dirname(__DIR__, 2);

    foreach ($candidates as $dir) {
        if (! is_dir($dir)) {
            continue;
        }
        $envFile = $dir . '/.env';
        if (is_readable($envFile)) {
            Dotenv\Dotenv::createImmutable($dir)->safeLoad();

            return;
        }
    }
}

function aquamarine_env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }

    return $value;
}
