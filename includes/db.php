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
    if ($name === null || $user === null) {
        $cached = false;

        return null;
    }

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

function aquamarine_db_available(): bool
{
    return aquamarine_db_config() !== null;
}

/**
 * @param array<string, mixed> $cfg
 * @return list<string>
 */
function aquamarine_db_hosts_to_try(array $cfg): array
{
    $host = (string) ($cfg['db_host'] ?? '127.0.0.1');
    $hosts = [$host];
    if ($host === 'localhost') {
        $hosts[] = '127.0.0.1';
    } elseif ($host === '127.0.0.1') {
        $hosts[] = 'localhost';
    }

    return array_values(array_unique($hosts));
}

/**
 * @param array<string, mixed> $cfg
 */
function aquamarine_pdo_connect(array $cfg): PDO
{
    $name = (string) ($cfg['db_name'] ?? '');
    $user = (string) ($cfg['db_user'] ?? '');
    $pass = (string) ($cfg['db_pass'] ?? '');
    $charset = (string) ($cfg['db_charset'] ?? 'utf8mb4');
    $port = (int) ($cfg['db_port'] ?? 3306);

    $last = null;
    foreach (aquamarine_db_hosts_to_try($cfg) as $host) {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $name, $charset);
        try {
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            $last = $e;
        }
    }

    if ($last instanceof PDOException) {
        throw $last;
    }

    throw new PDOException('MySQL connection failed');
}

/**
 * Verifică vendor, .env și conexiunea MySQL (pentru mesaje admin / CLI).
 *
 * @return array{ok: bool, message: string, details?: list<string>}
 */
function aquamarine_db_diagnose(): array
{
    $root = dirname(__DIR__);
    $details = [];
    $vendorOk = is_readable($root . '/vendor/autoload.php');
    $details[] = $vendorOk ? 'vendor/autoload.php: OK' : 'vendor/autoload.php: LIPSEȘTE';

    $envPath = aquamarine_find_env_file();
    if ($envPath === null) {
        return [
            'ok' => false,
            'message' => 'Fișierul <code>.env</code> nu a fost găsit în rădăcina site-ului sau în folderul părinte (ex. <code>/home/aquamari1/.env</code>).',
            'details' => $details,
        ];
    }
    $details[] = '.env: ' . $envPath;

    $name = aquamarine_env('DB_NAME');
    $user = aquamarine_env('DB_USER');
    if ($name === null || $user === null) {
        return [
            'ok' => false,
            'message' => 'În <code>.env</code> lipsesc <code>DB_NAME</code> sau <code>DB_USER</code> (verificați sintaxa fișierului).',
            'details' => $details,
        ];
    }

    $details[] = 'DB_NAME=' . $name;
    $details[] = 'DB_USER=' . $user;
    $details[] = 'DB_HOST=' . (aquamarine_env('DB_HOST', '127.0.0.1') ?? '127.0.0.1');
    $passLen = strlen(aquamarine_env('DB_PASS', '') ?? '');
    $details[] = 'DB_PASS: ' . $passLen . ' caractere (dacă parola reală are 13, dar aici e 11, ghilimelele nu s-au aplicat)';

    $cfg = aquamarine_db_config();
    if ($cfg === null) {
        return ['ok' => false, 'message' => 'Configurația DB din <code>.env</code> este incompletă.', 'details' => $details];
    }

    try {
        aquamarine_pdo_connect($cfg);
    } catch (PDOException $e) {
        $hint = '';
        $msg = $e->getMessage();
        $details[] = 'MySQL: ' . $msg;
        if (stripos($msg, 'Access denied') !== false) {
            $hint = ' User sau parolă greșită, sau userul nu e adăugat la bază în cPanel (Add User To Database → ALL PRIVILEGES). Parola cu <code>#</code> trebuie <code>DB_PASS="..."</code>.';
        } elseif (stripos($msg, 'Unknown database') !== false) {
            $hint = ' Baza <code>' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</code> nu există — creați-o în cPanel → MySQL Databases.';
        }

        return [
            'ok' => false,
            'message' => 'Conexiunea MySQL a eșuat.' . $hint,
            'details' => $details,
        ];
    }

    $details[] = 'MySQL: conexiune OK';

    return ['ok' => true, 'message' => '', 'details' => $details];
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

    $name = (string) ($cfg['db_name'] ?? '');
    $user = (string) ($cfg['db_user'] ?? '');

    if ($name === '' || $user === '') {
        $failed = true;

        return null;
    }

    try {
        $pdo = aquamarine_pdo_connect($cfg);
    } catch (PDOException $e) {
        error_log('Aquamarine DB connection failed: ' . $e->getMessage());
        $failed = true;

        return null;
    }

    return $pdo;
}
