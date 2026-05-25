<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/admin/bootstrap.php';
admin_require_auth();

$id = trim((string) ($_GET['id'] ?? ''));
$lead = $id !== '' ? leads_find($pdo, $id) : null;

if ($lead === null) {
    flash_set('admin', 'danger', 'Lead inexistent.');
    header('Location: leads.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    $status = (string) ($_POST['status'] ?? 'new');
    $notes = trim((string) ($_POST['admin_notes'] ?? ''));
    $markContacted = isset($_POST['mark_contacted']);
    leads_update_crm($pdo, $id, $status, $notes, $markContacted);
    flash_set('admin', 'success', 'Lead actualizat.');
    header('Location: lead.php?id=' . urlencode($id));
    exit;
}

$adminPageTitle = 'Lead: ' . (string) $lead['name'];
$adminCurrentNav = 'leads';
$attachments = leads_parse_attachments($lead);

$adminContent = static function () use ($lead, $attachments, $id): void {
    $phone = (string) ($lead['phone'] ?? '');
    $tel = preg_replace('/\D+/', '', $phone) ?? '';
    ?>
    <p class="mt-2 text-sm text-slate-500">
        Primit: <?= esc((string) ($lead['created_at'] ?? '')) ?>
        · Status: <?= esc(leads_status_label((string) ($lead['status'] ?? 'new'))) ?>
    </p>

    <dl class="mt-6 grid gap-3 rounded-xl border border-slate-200 bg-white p-5 text-sm sm:grid-cols-2">
        <div><dt class="text-slate-500">Nume</dt><dd class="font-medium"><?= esc((string) ($lead['name'] ?? '')) ?></dd></div>
        <div><dt class="text-slate-500">Telefon</dt><dd>
            <?php if ($tel !== '') { ?>
                <a href="tel:<?= esc($tel) ?>" class="font-medium text-cyan-700"><?= esc($phone) ?></a>
            <?php } else {
                echo esc($phone);
            } ?>
        </dd></div>
        <div><dt class="text-slate-500">Email</dt><dd><?= esc((string) ($lead['email'] ?? '—')) ?></dd></div>
        <div><dt class="text-slate-500">Magazin preferat</dt><dd><?= esc((string) ($lead['preferred_mag'] ?? '—')) ?></dd></div>
        <div class="sm:col-span-2"><dt class="text-slate-500">Serviciu</dt><dd><?= esc((string) ($lead['service_interest'] ?? '—')) ?></dd></div>
        <div class="sm:col-span-2"><dt class="text-slate-500">Mesaj</dt><dd class="mt-1 whitespace-pre-wrap"><?= esc((string) ($lead['message'] ?? '—')) ?></dd></div>
    </dl>

    <?php if ($attachments !== []) { ?>
        <section class="mt-6">
            <h2 class="font-semibold">Atașamente</h2>
            <ul class="mt-2 flex flex-wrap gap-3">
                <?php foreach ($attachments as $i => $path) { ?>
                    <li>
                        <a class="text-sm text-cyan-700 hover:underline" href="attachment.php?lead=<?= esc(urlencode($id)) ?>&amp;file=<?= (int) $i ?>">Poză <?= (int) $i + 1 ?></a>
                    </li>
                <?php } ?>
            </ul>
        </section>
    <?php } ?>

    <form method="post" class="mt-8 max-w-xl rounded-xl border border-slate-200 bg-white p-5">
        <h2 class="font-semibold">CRM</h2>
        <?= admin_csrf_field() ?>
        <label class="mt-4 block text-sm text-slate-600" for="status">Status</label>
        <select name="status" id="status" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <?php foreach (['new', 'contacted', 'closed', 'spam'] as $s) { ?>
                <option value="<?= esc($s) ?>" <?= ($lead['status'] ?? '') === $s ? 'selected' : '' ?>><?= esc(leads_status_label($s)) ?></option>
            <?php } ?>
        </select>
        <label class="mt-4 block text-sm text-slate-600" for="admin_notes">Note interne</label>
        <textarea name="admin_notes" id="admin_notes" rows="4" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"><?= esc((string) ($lead['admin_notes'] ?? '')) ?></textarea>
        <label class="mt-4 flex items-center gap-2 text-sm">
            <input type="checkbox" name="mark_contacted" value="1"> Marchează ca contactat acum
        </label>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white">Salvează</button>
            <a href="leads.php" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">Înapoi la listă</a>
        </div>
    </form>
    <?php
};

require dirname(__DIR__) . '/includes/admin/layout.php';
