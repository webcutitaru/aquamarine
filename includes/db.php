<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';
aquamarine_load_env();

/**
 * @return array<string, mixed>|null
 */
function aquamarine_db_config(): ?array
{
    static $cached = null;
    if ($cached !== null) {
        return $cached === false ? null : $cached;
    }

    $name = aquamarine_env('DB_NAME');
    $user = aquamarine_env('DB_USER');
    if ($name !== null && $user !== null) {
        $cached = [
            'db_host' => aquamarine_env('DB_HOST', '127.0.0.1'),
            'db_name' => $name,
            'db_user' => $user,
            'db_pass' => aquamarine_env('DB_PASS', ''),
            'db_charset' => aquamarine_env('DB_CHARSET', 'utf8mb4'),
            'db_port' => (int) (aquamarine_env('DB_PORT', '3306') ?? '3306'),
        ];

        return $cached;
    }

    $path = __DIR__ . '/config.local.php';
    if (! is_readable($path)) {
        $cached = false;

        return null;
    }

    $local = require $path;
    if (! is_array($local)) {
        $cached = false;

        return null;
    }

    $cached = $local;

    return $cached;
}

function aquamarine_db_available(): bool
{
    return aquamarine_db_config() !== null;
}

function aquamarine_pdo(): ?PDO
{
    static $pdo = null;
    static $failed = false;

    if ($failed) {
        return null;
    }

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $cfg = aquamarine_db_config();
    if ($cfg === null) {
        $failed = true;

        return null;
    }

    $host = (string) ($cfg['db_host'] ?? '127.0.0.1');
    $name = (string) ($cfg['db_name'] ?? '');
    $user = (string) ($cfg['db_user'] ?? '');
    $pass = (string) ($cfg['db_pass'] ?? '');
    $charset = (string) ($cfg['db_charset'] ?? 'utf8mb4');
    $port = (int) ($cfg['db_port'] ?? 3306);

    if ($name === '' || $user === '') {
        $failed = true;

        return null;
    }

    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $name, $charset);

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        error_log('Aquamarine DB connection failed: ' . $e->getMessage());
        $failed = true;

        return null;
    }

    return $pdo;
}
