<?php

declare(strict_types=1);

/** @var string $adminPageTitle */
/** @var string $adminCurrentNav */
/** @var callable(): void $adminContent */

$navItems = [
    'dashboard' => ['label' => 'Dashboard', 'href' => 'index.php'],
    'preturi' => ['label' => 'Prețuri', 'href' => 'preturi.php'],
    'oferte' => ['label' => 'Oferte', 'href' => 'oferte.php'],
    'leads' => ['label' => 'Lead-uri', 'href' => 'leads.php'],
];
$feedback = flash_pull('admin');
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title><?= esc($adminPageTitle) ?> — Aquamarine Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#ecfeff', 100: '#cffafe', 600: '#0891b2', 700: '#0e7490', 800: '#155e75' },
                        ink: '#0f172a',
                    },
                    fontFamily: { sans: ['DM Sans', 'system-ui', 'sans-serif'] },
                },
            },
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-slate-100 font-sans text-ink antialiased">
<div class="flex min-h-screen">
    <aside class="hidden w-56 shrink-0 flex-col border-r border-slate-200 bg-white p-4 lg:flex lg:flex-col">
        <p class="text-lg font-bold text-brand-700">Aquamarine</p>
        <p class="text-xs text-slate-500">Panou administrare</p>
        <nav class="mt-8 flex flex-col gap-1" aria-label="Admin">
            <?php foreach ($navItems as $key => $item) {
                $active = $adminCurrentNav === $key;
                ?>
                <a href="<?= esc($item['href']) ?>"
                   class="rounded-lg px-3 py-2 text-sm font-medium <?= $active ? 'bg-brand-50 text-brand-800' : 'text-slate-600 hover:bg-slate-50' ?>">
                    <?= esc($item['label']) ?>
                </a>
            <?php } ?>
        </nav>
        <div class="mt-auto border-t border-slate-100 pt-4 text-xs text-slate-500">
            <p><?= esc(admin_username()) ?></p>
            <a class="mt-2 inline-block text-brand-700 hover:underline" href="logout.php">Deconectare</a>
            <a class="mt-1 block text-slate-400 hover:underline" href="../index.php" target="_blank" rel="noopener">Vezi site</a>
        </div>
    </aside>

    <div class="flex flex-1 flex-col">
        <header class="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3 lg:hidden">
            <span class="font-bold text-brand-700">Admin</span>
            <a href="logout.php" class="text-sm text-brand-700">Ieșire</a>
        </header>
        <nav class="flex gap-2 overflow-x-auto border-b border-slate-200 bg-white px-4 py-2 lg:hidden">
            <?php foreach ($navItems as $key => $item) {
                $active = $adminCurrentNav === $key;
                ?>
                <a href="<?= esc($item['href']) ?>"
                   class="whitespace-nowrap rounded-full px-3 py-1 text-xs font-medium <?= $active ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-600' ?>">
                    <?= esc($item['label']) ?>
                </a>
            <?php } ?>
        </nav>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <h1 class="text-2xl font-bold tracking-tight"><?= esc($adminPageTitle) ?></h1>

            <?php if (($feedback['message'] ?? '') !== '') {
                $type = ($feedback['type'] ?? '') === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-rose-200 bg-rose-50 text-rose-900';
                ?>
                <p class="mt-4 rounded-lg border px-4 py-3 text-sm <?= esc($type) ?>" role="status">
                    <?= esc((string) $feedback['message']) ?>
                </p>
            <?php } ?>

            <div class="mt-6">
                <?php $adminContent(); ?>
            </div>
        </main>
    </div>
</div>
</body>
</html>
