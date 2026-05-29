<?php

declare(strict_types=1);

function admin_csrf_token(): string
{
    aquamarine_session_start();

    if (! isset($_SESSION['admin_csrf']) || ! is_string($_SESSION['admin_csrf']) || $_SESSION['admin_csrf'] === '') {
        $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['admin_csrf'];
}

function admin_csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . esc(admin_csrf_token()) . '">';
}

function admin_csrf_verify(): bool
{
    aquamarine_session_start();

    $sent = $_POST['_csrf'] ?? '';
    $expected = $_SESSION['admin_csrf'] ?? '';

    return is_string($sent) && is_string($expected) && $expected !== '' && hash_equals($expected, $sent);
}

function public_csrf_token(): string
{
    aquamarine_session_start();

    if (! isset($_SESSION['public_csrf']) || ! is_string($_SESSION['public_csrf']) || $_SESSION['public_csrf'] === '') {
        $_SESSION['public_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['public_csrf'];
}

function public_csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . esc(public_csrf_token()) . '">';
}

function public_csrf_verify(): bool
{
    aquamarine_session_start();

    $sent = $_POST['_csrf'] ?? '';
    $expected = $_SESSION['public_csrf'] ?? '';

    return is_string($sent) && is_string($expected) && $expected !== '' && hash_equals($expected, $sent);
}
