<?php

declare(strict_types=1);

require_once __DIR__ . '/csrf.php';

function admin_is_logged_in(): bool
{
    return ! empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function admin_require_auth(): void
{
    if (! admin_is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function admin_login_url(): string
{
    return 'login.php';
}

function admin_attempt_login(PDO $pdo, string $username, string $password): bool
{
    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admin_user WHERE username = ? LIMIT 1');
    $stmt->execute([trim($username)]);
    $row = $stmt->fetch();
    if (! is_array($row)) {
        return false;
    }

    $hash = (string) ($row['password_hash'] ?? '');
    if ($hash === '' || ! password_verify($password, $hash)) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = (string) $row['username'];
    unset($_SESSION['admin_csrf']);
    admin_csrf_token();

    return true;
}

function admin_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], (bool) $p['secure'], (bool) $p['httponly']);
    }
    session_destroy();
}

function admin_login_rate_limited(): bool
{
    $attempts = $_SESSION['admin_login_attempts'] ?? [];
    if (! is_array($attempts)) {
        return false;
    }
    $now = time();
    $recent = array_filter($attempts, static fn ($t) => is_int($t) && ($now - $t) < 900);
    $_SESSION['admin_login_attempts'] = array_values($recent);

    return count($recent) >= 5;
}

function admin_record_failed_login(): void
{
    $attempts = $_SESSION['admin_login_attempts'] ?? [];
    if (! is_array($attempts)) {
        $attempts = [];
    }
    $attempts[] = time();
    $_SESSION['admin_login_attempts'] = $attempts;
}

function admin_username(): string
{
    return (string) ($_SESSION['admin_username'] ?? 'admin');
}
