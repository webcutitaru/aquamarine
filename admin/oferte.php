<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/admin/bootstrap.php';
admin_require_auth();

$adminPageTitle = 'Bannere homepage';
$adminCurrentNav = 'oferte';

$offersDir = dirname(__DIR__) . '/assets/images/oferte';
$defaultContent = offers_default_content_seed();

function admin_process_offer_upload(string $targetDir): ?string
{
    if (! isset($_FILES['image']) || ! is_array($_FILES['image'])) {
        return null;
    }
    $file = $_FILES['image'];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return null;
    }
    $size = (int) ($file['size'] ?? 0);
    if ($size <= 0 || $size > 2 * 1024 * 1024) {
        return null;
    }
    $tmp = (string) ($file['tmp_name'] ?? '');
    if ($tmp === '' || ! is_uploaded_file($tmp)) {
        return null;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmp);
    $map = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    if (! is_string($mime) || ! isset($map[$mime])) {
        return null;
    }
    if (! is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $base = bin2hex(random_bytes(8)) . '.' . $map[$mime];
    $dest = $targetDir . '/' . $base;
    if (! move_uploaded_file($tmp, $dest)) {
        return null;
    }

    return 'assets/images/oferte/' . $base;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    $action = (string) ($_POST['action'] ?? '');

    try {
        if ($action === 'create') {
            $alt = trim((string) ($_POST['alt'] ?? ''));
            $altRu = trim((string) ($_POST['alt_ru'] ?? ''));
            $href = trim((string) ($_POST['href'] ?? ''));
            $active = isset($_POST['is_active']);
            $sort = (int) ($_POST['sort_order'] ?? 0);
            $content = offers_content_from_post();
            $imagePath = admin_process_offer_upload($offersDir);
            if ($imagePath === null) {
                flash_set('admin', 'danger', 'Încărcați o imagine validă (JPG, PNG sau WebP, max 2 MB).');
            } elseif ($content['heading'] === '') {
                flash_set('admin', 'danger', 'Titlul (RO) este obligatoriu.');
            } else {
                offers_create($pdo, $imagePath, $alt, $href, $active, $sort, $altRu, $content);
                flash_set('admin', 'success', 'Banner adăugat.');
            }
        } elseif ($action === 'update') {
            $id = (int) ($_POST['offer_id'] ?? 0);
            if ($id > 0) {
                $alt = trim((string) ($_POST['alt'] ?? ''));
                $altRu = trim((string) ($_POST['alt_ru'] ?? ''));
                $href = trim((string) ($_POST['href'] ?? ''));
                $active = isset($_POST['is_active']);
                $sort = (int) ($_POST['sort_order'] ?? 0);
                $content = offers_content_from_post();
                $newImage = admin_process_offer_upload($offersDir);
                offers_update($pdo, $id, $alt, $href, $active, $sort, $newImage, $altRu, $content);
                flash_set('admin', 'success', 'Banner actualizat.');
            }
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['offer_id'] ?? 0);
            if ($id > 0) {
                offers_delete($pdo, $id);
                flash_set('admin', 'success', 'Banner șters.');
            }
        }
    } catch (Throwable $e) {
        flash_set('admin', 'danger', 'Eroare la salvare.');
    }

    header('Location: oferte.php');
    exit;
}

$offers = offers_fetch_all_admin($pdo);

$adminContent = static function () use ($offers, $defaultContent): void {
    ?>
    <p class="mt-2 text-sm text-slate-600">
        Textele de mai jos (etichetă, titlu, descriere) apar peste imagine pe homepage și se schimbă la fiecare diapozitiv din carusel.
    </p>
    <form method="post" enctype="multipart/form-data" class="mt-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5">
        <h2 class="font-semibold">Banner nou</h2>
        <?= admin_csrf_field() ?>
        <input type="hidden" name="action" value="create">
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <div>
                <label class="text-xs text-slate-500">Imagine *</label>
                <input class="mt-1 block w-full text-sm" type="file" name="image" accept="image/jpeg,image/png,image/webp" required>
            </div>
            <div>
                <label class="text-xs text-slate-500">Ordine</label>
                <input class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" type="number" name="sort_order" value="0">
            </div>
            <div>
                <label class="text-xs text-slate-500">Etichetă (eyebrow) RO</label>
                <input class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" name="eyebrow" value="<?= esc($defaultContent['eyebrow']) ?>">
            </div>
            <div>
                <label class="text-xs text-slate-500">Etichetă (eyebrow) RU</label>
                <input class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" name="eyebrow_ru" value="<?= esc($defaultContent['eyebrow_ru']) ?>">
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs text-slate-500">Titlu (RO) *</label>
                <input class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" name="heading" value="<?= esc($defaultContent['heading']) ?>" required>
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs text-slate-500">Titlu (RU)</label>
                <input class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" name="heading_ru" value="<?= esc($defaultContent['heading_ru']) ?>">
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs text-slate-500">Descriere (RO)</label>
                <textarea class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" name="sub" rows="3"><?= esc($defaultContent['sub']) ?></textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs text-slate-500">Descriere (RU)</label>
                <textarea class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" name="sub_ru" rows="3"><?= esc($defaultContent['sub_ru']) ?></textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs text-slate-500">Text alternativ imagine (RO)</label>
                <input class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" name="alt" required>
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs text-slate-500">Text alternativ imagine (RU)</label>
                <input class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" name="alt_ru" placeholder="Opțional — pentru versiunea /ru/">
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs text-slate-500">Link (href)</label>
                <input class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" name="href" placeholder="servicii-si-preturi.php" value="servicii-si-preturi.php">
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" checked> Activ pe homepage
            </label>
        </div>
        <button type="submit" class="mt-4 rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white">Adaugă banner</button>
    </form>

    <div class="mt-8 space-y-6">
        <?php foreach ($offers as $offer) {
            if (! is_array($offer)) {
                continue;
            }
            $id = (int) $offer['id'];
            ?>
            <form method="post" enctype="multipart/form-data" class="rounded-xl border border-slate-200 bg-white p-5">
                <?= admin_csrf_field() ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="offer_id" value="<?= $id ?>">
                <div class="flex flex-wrap gap-6">
                    <img src="../<?= esc((string) $offer['image_path']) ?>" alt="" class="h-24 w-auto rounded-lg border object-cover">
                    <div class="min-w-0 flex-1 grid gap-3 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="text-xs text-slate-500">Înlocuiește imagine (opțional)</label>
                            <input class="mt-1 block w-full text-sm" type="file" name="image" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Etichetă RO</label>
                            <input class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="eyebrow" value="<?= esc((string) ($offer['eyebrow'] ?? $defaultContent['eyebrow'])) ?>">
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Etichetă RU</label>
                            <input class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="eyebrow_ru" value="<?= esc((string) ($offer['eyebrow_ru'] ?? $defaultContent['eyebrow_ru'])) ?>">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs text-slate-500">Titlu RO</label>
                            <input class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="heading" value="<?= esc((string) ($offer['heading'] ?? '')) ?>">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs text-slate-500">Titlu RU</label>
                            <input class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="heading_ru" value="<?= esc((string) ($offer['heading_ru'] ?? '')) ?>">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs text-slate-500">Descriere RO</label>
                            <textarea class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="sub" rows="3"><?= esc((string) ($offer['sub'] ?? '')) ?></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs text-slate-500">Descriere RU</label>
                            <textarea class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="sub_ru" rows="3"><?= esc((string) ($offer['sub_ru'] ?? '')) ?></textarea>
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Alt imagine (RO)</label>
                            <input class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="alt" value="<?= esc((string) $offer['alt']) ?>">
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Alt imagine (RU)</label>
                            <input class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="alt_ru" value="<?= esc((string) ($offer['alt_ru'] ?? '')) ?>">
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Href</label>
                            <input class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" name="href" value="<?= esc((string) $offer['href']) ?>">
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Ordine</label>
                            <input class="mt-1 w-full rounded border border-slate-300 px-2 py-1.5 text-sm" type="number" name="sort_order" value="<?= (int) ($offer['sort_order'] ?? 0) ?>">
                        </div>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_active" value="1" <?= ((int) ($offer['is_active'] ?? 0)) === 1 ? 'checked' : '' ?>> Activ
                        </label>
                    </div>
                </div>
                <div class="mt-4 flex gap-3">
                    <button type="submit" class="rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white">Salvează</button>
                </div>
            </form>
            <form method="post" class="ml-5 -mt-4" onsubmit="return confirm('Ștergeți bannerul?');">
                <?= admin_csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="offer_id" value="<?= $id ?>">
                <button type="submit" class="text-xs text-rose-600 hover:underline">Șterge banner</button>
            </form>
        <?php } ?>
    </div>
    <?php
};

require dirname(__DIR__) . '/includes/admin/layout.php';
