<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/admin/bootstrap.php';

if (admin_is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (admin_login_rate_limited()) {
        $error = 'Prea multe încercări. Încercați din nou peste 15 minute.';
    } elseif (! admin_csrf_verify()) {
        $error = 'Sesiune invalidă. Reîncărcați pagina.';
    } else {
        $user = trim((string) ($_POST['username'] ?? ''));
        $pass = (string) ($_POST['password'] ?? '');
        if ($user === '' || $pass === '') {
            $error = 'Completați utilizatorul și parola.';
        } elseif (admin_attempt_login($pdo, $user, $pass)) {
            header('Location: index.php');
            exit;
        } else {
            admin_record_failed_login();
            $error = 'Date de autentificare incorecte.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Autentificare — Aquamarine Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100 px-4">
    <form method="post" class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-8 shadow-lg">
        <h1 class="text-xl font-bold text-slate-900">Aquamarine Admin</h1>
        <p class="mt-1 text-sm text-slate-500">Autentificare panou</p>
        <?php if ($error !== null) { ?>
            <p class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800" role="alert"><?= esc($error) ?></p>
        <?php } ?>
        <?= admin_csrf_field() ?>
        <label class="mt-6 block text-sm font-medium text-slate-700" for="username">Utilizator</label>
        <input class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" type="text" name="username" id="username" required autocomplete="username">
        <label class="mt-4 block text-sm font-medium text-slate-700" for="password">Parolă</label>
        <input class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" type="password" name="password" id="password" required autocomplete="current-password">
        <button type="submit" class="mt-6 w-full rounded-lg bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">Intră</button>
    </form>
</body>
</html>
