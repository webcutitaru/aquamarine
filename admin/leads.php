<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/admin/bootstrap.php';
admin_require_auth();

$adminPageTitle = 'Lead-uri contact';
$adminCurrentNav = 'leads';

$status = isset($_GET['status']) ? (string) $_GET['status'] : '';
$mag = isset($_GET['mag']) ? (string) $_GET['mag'] : '';
$dateFrom = isset($_GET['from']) ? (string) $_GET['from'] : '';
$dateTo = isset($_GET['to']) ? (string) $_GET['to'] : '';

$leads = leads_list(
    $pdo,
    $status !== '' ? $status : null,
    $mag !== '' ? $mag : null,
    $dateFrom !== '' ? $dateFrom : null,
    $dateTo !== '' ? $dateTo : null
);

$cities = [];
foreach ($config['locations'] ?? [] as $loc) {
    if (is_array($loc) && isset($loc['city'])) {
        $cities[] = (string) $loc['city'];
    }
}

$adminContent = static function () use ($leads, $status, $mag, $dateFrom, $dateTo, $cities): void {
    ?>
    <form method="get" class="mt-4 flex flex-wrap items-end gap-3 rounded-xl border border-slate-200 bg-white p-4">
        <div>
            <label class="text-xs text-slate-500">Status</label>
            <select name="status" class="mt-1 rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Toate</option>
                <?php foreach (['new', 'contacted', 'closed', 'spam'] as $s) { ?>
                    <option value="<?= esc($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= esc(leads_status_label($s)) ?></option>
                <?php } ?>
            </select>
        </div>
        <div>
            <label class="text-xs text-slate-500">Magazin</label>
            <select name="mag" class="mt-1 rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Toate</option>
                <?php foreach ($cities as $city) { ?>
                    <option value="<?= esc($city) ?>" <?= $mag === $city ? 'selected' : '' ?>><?= esc($city) ?></option>
                <?php } ?>
            </select>
        </div>
        <div>
            <label class="text-xs text-slate-500">De la</label>
            <input type="date" name="from" class="mt-1 rounded-lg border border-slate-300 px-3 py-2 text-sm" value="<?= esc($dateFrom) ?>">
        </div>
        <div>
            <label class="text-xs text-slate-500">Până la</label>
            <input type="date" name="to" class="mt-1 rounded-lg border border-slate-300 px-3 py-2 text-sm" value="<?= esc($dateTo) ?>">
        </div>
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm text-white">Filtrează</button>
        <a href="leads.php" class="text-sm text-slate-500 hover:underline">Reset</a>
    </form>

    <div class="mt-6 overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-slate-100 bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3">Data</th>
                    <th class="px-4 py-3">Nume</th>
                    <th class="px-4 py-3">Telefon</th>
                    <th class="px-4 py-3">Magazin</th>
                    <th class="px-4 py-3">Serviciu</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($leads === []) { ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Niciun rezultat.</td></tr>
                <?php } ?>
                <?php foreach ($leads as $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $phone = (string) ($row['phone'] ?? '');
                    $tel = preg_replace('/\D+/', '', $phone) ?? '';
                    ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="px-4 py-3 whitespace-nowrap"><?= esc((string) ($row['created_at'] ?? '')) ?></td>
                        <td class="px-4 py-3">
                            <a class="font-medium text-cyan-700 hover:underline" href="lead.php?id=<?= esc(urlencode((string) ($row['id'] ?? ''))) ?>">
                                <?= esc((string) ($row['name'] ?? '')) ?>
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <?php if ($tel !== '') { ?>
                                <a href="tel:<?= esc($tel) ?>" class="hover:underline"><?= esc($phone) ?></a>
                            <?php } else {
                                echo esc($phone);
                            } ?>
                        </td>
                        <td class="px-4 py-3"><?= esc((string) ($row['preferred_mag'] ?? '—')) ?></td>
                        <td class="px-4 py-3"><?= esc((string) ($row['service_interest'] ?? '—')) ?></td>
                        <td class="px-4 py-3"><?= esc(leads_status_label((string) ($row['status'] ?? 'new'))) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
};

require dirname(__DIR__) . '/includes/admin/layout.php';
