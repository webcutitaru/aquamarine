<?php

declare(strict_types=1);

/**
 * Copiați ca includes/config.local.php (nu se versionează).
 *
 * Setup MAMP:
 * 1. phpMyAdmin → creați baza `aquamarine`
 * 2. Importați database/schema.sql
 * 3. CLI: php database/seed_from_json.php
 * 4. CLI: php database/create_admin.php admin ParolaVoastraSigura
 * 5. Opțional: php database/import_leads_ndjson.php
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
    // Opțional: suprascrieți setările B2B din config.php
    // 'b2b_employee_discount_percent' => 12,
    // 'b2b_delivery_note' => '...',
];
