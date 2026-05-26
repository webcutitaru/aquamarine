<?php

declare(strict_types=1);

/**
 * @deprecated Folosiți .env — vezi .env.example
 *
 * Copiați ca includes/config.local.php doar dacă nu folosiți .env (fallback).
 * Preferat: cp .env.example .env și completați valorile.
 *
 * Setup MAMP:
 * 1. composer install
 * 2. cp .env.example .env
 * 3. phpMyAdmin → creați baza `aquamarine`
 * 4. Importați database/schema.sql
 * 5. CLI: php database/seed_from_json.php
 * 6. CLI: php database/create_admin.php admin ParolaVoastraSigura
 *
 * Panou: /admin/login.php
 */
return [
    'db_host' => '127.0.0.1',
    'db_name' => 'aquamarine',
    'db_user' => 'root',
    /** MAMP: port 8889, parolă root */
    'db_pass' => 'root',
    'db_charset' => 'utf8mb4',
    'db_port' => 8889,
];
