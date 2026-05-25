<?php

declare(strict_types=1);

/**
 * CLI: php database/create_admin.php [username] [password]
 * Dacă parola lipsește, se generează una aleatorie (afișată o singură dată).
 */

$root = dirname(__DIR__);
require $root . '/includes/db.php';

$pdo = aquamarine_pdo();
if (! $pdo instanceof PDO) {
    fwrite(STDERR, "Eroare: config DB lipsă.\n");
    exit(1);
}

$username = $argv[1] ?? 'admin';
$password = $argv[2] ?? null;

if ($password === null || $password === '') {
    $password = bin2hex(random_bytes(8));
    $generated = true;
} else {
    $generated = false;
}

if (strlen($password) < 10) {
    fwrite(STDERR, "Parola trebuie să aibă minim 10 caractere.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$pdo->exec('DELETE FROM admin_user');
$stmt = $pdo->prepare('INSERT INTO admin_user (username, password_hash) VALUES (?, ?)');
$stmt->execute([$username, $hash]);

echo "Cont admin creat.\n";
echo "Utilizator: {$username}\n";
if ($generated) {
    echo "Parolă generată (salvați-o acum): {$password}\n";
} else {
    echo "Parola setată din argument CLI.\n";
}
