<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/admin/bootstrap.php';
admin_require_auth();

$adminPageTitle = 'Prețuri și servicii';
$adminCurrentNav = 'preturi';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    $action = (string) ($_POST['action'] ?? '');

    try {
        if ($action === 'save_settings') {
            pricing_save_settings(
                $pdo,
                trim((string) ($_POST['note'] ?? '')),
                trim((string) ($_POST['currency'] ?? 'MDL')),
                trim((string) ($_POST['note_ru'] ?? '')) ?: null
            );
            flash_set('admin', 'success', 'Setări catalog salvate.');
        } elseif ($action === 'add_category') {
            $name = trim((string) ($_POST['name'] ?? ''));
            if ($name !== '') {
                $max = (int) $pdo->query('SELECT COALESCE(MAX(sort_order), -1) + 1 FROM price_categories')->fetchColumn();
                pricing_create_category(
                    $pdo,
                    $name,
                    trim((string) ($_POST['footnote'] ?? '')) ?: null,
                    $max,
                    trim((string) ($_POST['name_ru'] ?? '')) ?: null,
                    trim((string) ($_POST['footnote_ru'] ?? '')) ?: null
                );
                flash_set('admin', 'success', 'Categorie adăugată.');
            }
        } elseif ($action === 'update_category') {
            $id = (int) ($_POST['category_id'] ?? 0);
            $name = trim((string) ($_POST['name'] ?? ''));
            if ($id > 0 && $name !== '') {
                pricing_update_category(
                    $pdo,
                    $id,
                    $name,
                    trim((string) ($_POST['footnote'] ?? '')) ?: null,
                    (int) ($_POST['sort_order'] ?? 0),
                    trim((string) ($_POST['name_ru'] ?? '')) ?: null,
                    trim((string) ($_POST['footnote_ru'] ?? '')) ?: null
                );
                flash_set('admin', 'success', 'Categorie actualizată.');
            }
        } elseif ($action === 'delete_category') {
            $id = (int) ($_POST['category_id'] ?? 0);
            if ($id > 0) {
                pricing_delete_category($pdo, $id);
                flash_set('admin', 'success', 'Categorie ștearsă.');
            }
        } elseif ($action === 'add_item') {
            $catId = (int) ($_POST['category_id'] ?? 0);
            $service = trim((string) ($_POST['service'] ?? ''));
            $price = trim((string) ($_POST['price'] ?? ''));
            if ($catId > 0 && $service !== '' && $price !== '') {
                $stmt = $pdo->prepare('SELECT COALESCE(MAX(sort_order), -1) + 1 FROM price_items WHERE category_id = ?');
                $stmt->execute([$catId]);
                $sort = (int) $stmt->fetchColumn();
                pricing_create_item(
                    $pdo,
                    $catId,
                    $service,
                    $price,
                    trim((string) ($_POST['description'] ?? '')) ?: null,
                    trim((string) ($_POST['note'] ?? '')) ?: null,
                    $sort,
                    trim((string) ($_POST['service_ru'] ?? '')) ?: null,
                    trim((string) ($_POST['description_ru'] ?? '')) ?: null,
                    trim((string) ($_POST['note_ru'] ?? '')) ?: null
                );
                flash_set('admin', 'success', 'Serviciu adăugat.');
            }
        } elseif ($action === 'update_item') {
            $id = (int) ($_POST['item_id'] ?? 0);
            $service = trim((string) ($_POST['service'] ?? ''));
            $price = trim((string) ($_POST['price'] ?? ''));
            if ($id > 0 && $service !== '' && $price !== '') {
                pricing_update_item(
                    $pdo,
                    $id,
                    $service,
                    $price,
                    trim((string) ($_POST['description'] ?? '')) ?: null,
                    trim((string) ($_POST['note'] ?? '')) ?: null,
                    (int) ($_POST['sort_order'] ?? 0),
                    trim((string) ($_POST['service_ru'] ?? '')) ?: null,
                    trim((string) ($_POST['description_ru'] ?? '')) ?: null,
                    trim((string) ($_POST['note_ru'] ?? '')) ?: null
                );
                flash_set('admin', 'success', 'Serviciu actualizat.');
            }
        } elseif ($action === 'delete_item') {
            $id = (int) ($_POST['item_id'] ?? 0);
            if ($id > 0) {
                pricing_delete_item($pdo, $id);
                flash_set('admin', 'success', 'Serviciu șters.');
            }
        }
    } catch (Throwable $e) {
        flash_set('admin', 'danger', 'Eroare la salvare.');
    }

    header('Location: preturi.php');
    exit;
}

$catalog = pricing_fetch_catalog($pdo);
$categoriesAdmin = pricing_fetch_categories_admin($pdo);
$hasRuCols = pricing_db_has_ru_columns($pdo);
$noteRuVal = '';
if ($hasRuCols) {
    $settingsRow = $pdo->query('SELECT note_ru FROM price_settings WHERE id = 1')->fetch();
    $noteRuVal = is_array($settingsRow) ? (string) ($settingsRow['note_ru'] ?? '') : '';
}

$adminContent = static function () use ($catalog, $categoriesAdmin, $pdo, $hasRuCols, $noteRuVal): void {
    ?>
    <form method="post" class="mt-4 rounded-xl border border-slate-200 bg-white p-5">
        <h2 class="font-semibold">Notă catalog</h2>
        <?= admin_csrf_field() ?>
        <input type="hidden" name="action" value="save_settings">
        <label class="mt-3 block text-sm text-slate-600" for="note">Text introductiv</label>
        <textarea class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" name="note" id="note" rows="3"><?= esc($catalog['note']) ?></textarea>
        <?php if ($hasRuCols) { ?>
        <label class="mt-3 block text-sm text-slate-600" for="note_ru">Notă catalog (RU, opțional)</label>
        <textarea class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" name="note_ru" id="note_ru" rows="3"><?= esc($noteRuVal) ?></textarea>
        <?php } ?>
        <label class="mt-3 block text-sm text-slate-600" for="currency">Monedă</label>
        <input class="mt-1 w-32 rounded-lg border border-slate-300 px-3 py-2 text-sm" name="currency" id="currency" value="<?= esc($catalog['currency']) ?>">
        <button type="submit" class="mt-4 rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">Salvează setări</button>
    </form>

    <form method="post" class="mt-6 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5">
        <h2 class="font-semibold">Categorie nouă</h2>
        <?= admin_csrf_field() ?>
        <input type="hidden" name="action" value="add_category">
        <input class="mt-2 w-full max-w-md rounded-lg border border-slate-300 px-3 py-2 text-sm" name="name" placeholder="Nume categorie" required>
        <input class="mt-2 w-full max-w-md rounded-lg border border-slate-300 px-3 py-2 text-sm" name="footnote" placeholder="Notă subsol (opțional)">
        <button type="submit" class="mt-3 rounded-lg border border-cyan-700 px-4 py-2 text-sm font-semibold text-cyan-800 hover:bg-cyan-50">Adaugă categorie</button>
    </form>

    <?php foreach ($categoriesAdmin as $cat) {
        if (! is_array($cat)) {
            continue;
        }
        $catId = (int) $cat['id'];
        $items = pricing_fetch_items_by_category($pdo, $catId);
        ?>
        <section class="mt-8 rounded-xl border border-slate-200 bg-white p-5">
            <form method="post" class="flex flex-wrap items-end gap-3 border-b border-slate-100 pb-4">
                <?= admin_csrf_field() ?>
                <input type="hidden" name="action" value="update_category">
                <input type="hidden" name="category_id" value="<?= $catId ?>">
                <div class="min-w-[12rem] flex-1">
                    <label class="text-xs text-slate-500">Categorie</label>
                    <input class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold" name="name" value="<?= esc((string) $cat['name']) ?>">
                </div>
                <div>
                    <label class="text-xs text-slate-500">Ordine</label>
                    <input class="mt-1 w-20 rounded-lg border border-slate-300 px-2 py-2 text-sm" type="number" name="sort_order" value="<?= (int) ($cat['sort_order'] ?? 0) ?>">
                </div>
                <button type="submit" class="rounded-lg bg-slate-800 px-3 py-2 text-sm text-white">Salvează categorie</button>
                <div class="w-full basis-full">
                    <label class="text-xs text-slate-500">Notă subsol</label>
                    <input class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" name="footnote" value="<?= esc((string) ($cat['footnote'] ?? '')) ?>">
                </div>
                <?php if ($hasRuCols) { ?>
                <div class="w-full basis-full">
                    <label class="text-xs text-slate-500">Nume categorie (RU)</label>
                    <input class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" name="name_ru" value="<?= esc((string) ($cat['name_ru'] ?? '')) ?>">
                </div>
                <div class="w-full basis-full">
                    <label class="text-xs text-slate-500">Notă subsol (RU)</label>
                    <input class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" name="footnote_ru" value="<?= esc((string) ($cat['footnote_ru'] ?? '')) ?>">
                </div>
                <?php } ?>
            </form>
            <form method="post" class="mt-2 inline" onsubmit="return confirm('Ștergeți categoria și toate serviciile?');">
                <?= admin_csrf_field() ?>
                <input type="hidden" name="action" value="delete_category">
                <input type="hidden" name="category_id" value="<?= $catId ?>">
                <button type="submit" class="text-xs text-rose-600 hover:underline">Șterge categorie</button>
            </form>

            <div class="mt-6 space-y-4">
                <?php foreach ($items as $item) {
                    if (! is_array($item)) {
                        continue;
                    }
                    $itemId = (int) $item['id'];
                    ?>
                    <form method="post" class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                        <?= admin_csrf_field() ?>
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="item_id" value="<?= $itemId ?>">
                        <div class="grid gap-3 lg:grid-cols-2">
                            <div>
                                <label class="text-xs text-slate-500">Serviciu</label>
                                <input class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="service" value="<?= esc((string) $item['service']) ?>" required>
                            </div>
                            <div>
                                <label class="text-xs text-slate-500">Preț</label>
                                <input class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="price" value="<?= esc((string) $item['price']) ?>" required>
                            </div>
                            <div class="lg:col-span-2">
                                <label class="text-xs text-slate-500">Descriere</label>
                                <textarea class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="description" rows="2"><?= esc((string) ($item['description'] ?? '')) ?></textarea>
                            </div>
                            <?php if ($hasRuCols) { ?>
                            <div>
                                <label class="text-xs text-slate-500">Serviciu (RU)</label>
                                <input class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="service_ru" value="<?= esc((string) ($item['service_ru'] ?? '')) ?>">
                            </div>
                            <div class="lg:col-span-2">
                                <label class="text-xs text-slate-500">Descriere (RU)</label>
                                <textarea class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="description_ru" rows="2"><?= esc((string) ($item['description_ru'] ?? '')) ?></textarea>
                            </div>
                            <div class="lg:col-span-2">
                                <label class="text-xs text-slate-500">Notă (RU)</label>
                                <input class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="note_ru" value="<?= esc((string) ($item['note_ru'] ?? '')) ?>">
                            </div>
                            <?php } ?>
                            <div>
                                <label class="text-xs text-slate-500">Notă item</label>
                                <input class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="note" value="<?= esc((string) ($item['note'] ?? '')) ?>">
                            </div>
                            <div>
                                <label class="text-xs text-slate-500">Ordine</label>
                                <input class="mt-1 w-20 rounded border border-slate-300 px-2 py-1.5 text-sm" type="number" name="sort_order" value="<?= (int) ($item['sort_order'] ?? 0) ?>">
                            </div>
                        </div>
                        <div class="mt-3 flex gap-3">
                            <button type="submit" class="rounded bg-cyan-700 px-3 py-1.5 text-xs font-semibold text-white">Salvează</button>
                        </div>
                    </form>
                    <form method="post" class="-mt-2 ml-4 inline" onsubmit="return confirm('Ștergeți serviciul?');">
                        <?= admin_csrf_field() ?>
                        <input type="hidden" name="action" value="delete_item">
                        <input type="hidden" name="item_id" value="<?= $itemId ?>">
                        <button type="submit" class="text-xs text-rose-600 hover:underline">Șterge</button>
                    </form>
                <?php } ?>
            </div>

            <form method="post" class="mt-6 border-t border-slate-100 pt-4">
                <p class="text-sm font-medium text-slate-700">Serviciu nou în această categorie</p>
                <?= admin_csrf_field() ?>
                <input type="hidden" name="action" value="add_item">
                <input type="hidden" name="category_id" value="<?= $catId ?>">
                <div class="mt-2 grid gap-2 lg:grid-cols-2">
                    <input class="rounded border border-slate-300 px-2 py-1.5 text-sm" name="service" placeholder="Nume serviciu" required>
                    <input class="rounded border border-slate-300 px-2 py-1.5 text-sm" name="price" placeholder="Preț (ex. de la 120)" required>
                    <textarea class="lg:col-span-2 rounded border border-slate-300 px-2 py-1.5 text-sm" name="description" rows="2" placeholder="Descriere"></textarea>
                    <input class="rounded border border-slate-300 px-2 py-1.5 text-sm" name="note" placeholder="Notă opțională">
                </div>
                <button type="submit" class="mt-3 rounded-lg border border-cyan-700 px-3 py-1.5 text-sm font-semibold text-cyan-800">Adaugă serviciu</button>
            </form>
        </section>
    <?php } ?>
    <?php
};

require dirname(__DIR__) . '/includes/admin/layout.php';
