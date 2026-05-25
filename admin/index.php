<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/admin/bootstrap.php';
admin_require_auth();

$adminPageTitle = 'Dashboard';
$adminCurrentNav = 'dashboard';

$newCount = leads_count_by_status($pdo, 'new');
$recent = leads_recent($pdo, 5);

$adminContent = static function () use ($newCount, $recent): void {
    ?>
    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Lead-uri noi</p>
            <p class="mt-1 text-3xl font-bold text-cyan-700"><?= (int) $newCount ?></p>
        </div>
    </div>

    <section class="mt-8">
        <h2 class="text-lg font-semibold">Ultimele cereri</h2>
        <?php if ($recent === []) { ?>
            <p class="mt-2 text-sm text-slate-500">Nicio cerere încă.</p>
        <?php } else { ?>
            <div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-medium">Data</th>
                            <th class="px-4 py-3 font-medium">Nume</th>
                            <th class="px-4 py-3 font-medium">Telefon</th>
                            <th class="px-4 py-3 font-medium">Magazin</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent as $row) {
                            if (! is_array($row)) {
                                continue;
                            }
                            ?>
                            <tr class="border-b border-slate-50 hover:bg-slate-50">
                                <td class="px-4 py-3 whitespace-nowrap"><?= esc((string) ($row['created_at'] ?? '')) ?></td>
                                <td class="px-4 py-3">
                                    <a class="font-medium text-cyan-700 hover:underline" href="lead.php?id=<?= esc(urlencode((string) ($row['id'] ?? ''))) ?>">
                                        <?= esc((string) ($row['name'] ?? '')) ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3"><?= esc((string) ($row['phone'] ?? '')) ?></td>
                                <td class="px-4 py-3"><?= esc((string) ($row['preferred_mag'] ?? '—')) ?></td>
                                <td class="px-4 py-3"><?= esc(leads_status_label((string) ($row['status'] ?? 'new'))) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <p class="mt-3"><a href="leads.php" class="text-sm font-medium text-cyan-700 hover:underline">Toate lead-urile →</a></p>
        <?php } ?>
    </section>
    <?php
};

require dirname(__DIR__) . '/includes/admin/layout.php';
