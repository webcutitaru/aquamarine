<?php

declare(strict_types=1);

require __DIR__ . '/includes/init.php';

$c = lang_data('contact');
/** @var array<string, string> $contactErrors */
$contactErrors = isset($c['errors']) && is_array($c['errors']) ? $c['errors'] : [];

/**
 * @param array<string, string> $errMsgs
 * @return array{0: list<string>, 1: list<string>} [errors, relative_paths]
 */
function contact_process_photo_uploads(array $errMsgs): array
{
    $errors = [];
    $savedRel = [];
    if (! isset($_FILES['photos']) || ! is_array($_FILES['photos']['name'])) {
        return [$errors, $savedRel];
    }

    $names = $_FILES['photos']['name'];
    $tmpNames = $_FILES['photos']['tmp_name'];
    $sizes = $_FILES['photos']['size'];
    $errs = $_FILES['photos']['error'];
    if (! is_array($names)) {
        $names = [$names];
        $tmpNames = [$tmpNames];
        $sizes = [$sizes];
        $errs = [$errs];
    }

    $maxFiles = 3;
    $maxBytes = 4 * 1024 * 1024;
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    $n = min(count($names), $maxFiles);
    for ($i = 0; $i < $n; $i++) {
        $err = (int) ($errs[$i] ?? UPLOAD_ERR_NO_FILE);
        if ($err === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($err !== UPLOAD_ERR_OK) {
            $errors[] = $errMsgs['upload_fail'] ?? '';
            break;
        }
        $size = (int) ($sizes[$i] ?? 0);
        if ($size > $maxBytes) {
            $errors[] = $errMsgs['file_size'] ?? '';
            break;
        }
        $tmp = (string) ($tmpNames[$i] ?? '');
        if ($tmp === '' || ! is_uploaded_file($tmp)) {
            $errors[] = $errMsgs['file_invalid'] ?? '';
            break;
        }
        $mime = '';
        if (class_exists(finfo::class)) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = (string) $finfo->file($tmp);
        } elseif (function_exists('mime_content_type')) {
            $mime = (string) mime_content_type($tmp);
        }
        if ($mime === '' || ! isset($allowed[$mime])) {
            $errors[] = $errMsgs['file_type'] ?? '';
            break;
        }
        $ext = $allowed[$mime];
        $dir = __DIR__ . '/data/contact_uploads';
        if (! is_dir($dir) && ! @mkdir($dir, 0755, true)) {
            $errors[] = $errMsgs['save_fail'] ?? '';
            break;
        }
        $base = bin2hex(random_bytes(12)) . '.' . $ext;
        $dest = $dir . '/' . $base;
        if (! move_uploaded_file($tmp, $dest)) {
            $errors[] = $errMsgs['save_image'] ?? '';
            break;
        }
        $savedRel[] = 'contact_uploads/' . $base;
    }

    return [$errors, $savedRel];
}

function contact_rate_limited(): bool
{
    $attempts = $_SESSION['contact_submit_attempts'] ?? [];
    if (! is_array($attempts)) {
        return false;
    }
    $now = time();
    $recent = array_filter($attempts, static fn ($t) => is_int($t) && ($now - $t) < 3600);
    $_SESSION['contact_submit_attempts'] = array_values($recent);

    return count($recent) >= 5;
}

function contact_record_submit(): void
{
    $attempts = $_SESSION['contact_submit_attempts'] ?? [];
    if (! is_array($attempts)) {
        $attempts = [];
    }
    $attempts[] = time();
    $_SESSION['contact_submit_attempts'] = $attempts;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($postCompany = ($_POST['company'] ?? '')) !== '') {
        flash_set('contact', 'danger', (string) ($contactErrors['spam'] ?? ''));
        header('Location: ' . aquamarine_url('contact.php'));
        exit;
    }

    if (! public_csrf_verify()) {
        flash_set('contact', 'danger', (string) ($contactErrors['csrf'] ?? ''));
        header('Location: ' . aquamarine_url('contact.php') . '#form');
        exit;
    }

    if (contact_rate_limited()) {
        flash_set('contact', 'danger', (string) ($contactErrors['rate_limit'] ?? ''));
        header('Location: ' . aquamarine_url('contact.php') . '#form');
        exit;
    }

    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $service = trim((string) ($_POST['service'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));
    $preferredMag = trim((string) ($_POST['preferred_mag'] ?? ''));

    $errors = [];

    if (mb_strlen($fullName, 'UTF-8') < 2) {
        $errors[] = (string) ($contactErrors['name'] ?? '');
    }

    $digits = preg_replace('/\D/', '', $phone) ?: '';
    if (mb_strlen($digits, 'UTF-8') < 8) {
        $errors[] = (string) ($contactErrors['phone'] ?? '');
    }

    if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = (string) ($contactErrors['email'] ?? '');
    }

    if (mb_strlen($message, 'UTF-8') < 4) {
        $errors[] = (string) ($contactErrors['message_short'] ?? '');
    }

    if (mb_strlen($message, 'UTF-8') > 4000) {
        $errors[] = (string) ($contactErrors['message_long'] ?? '');
    }

    $allowedCities = [];
    if (isset($config['locations']) && is_array($config['locations'])) {
        foreach ($config['locations'] as $loc) {
            if (is_array($loc) && isset($loc['city']) && (string) $loc['city'] !== '') {
                $allowedCities[] = (string) $loc['city'];
            }
        }
    }
    if ($preferredMag !== '' && ! in_array($preferredMag, $allowedCities, true)) {
        $errors[] = (string) ($contactErrors['store'] ?? '');
    }

    if (! isset($_POST['gdpr_confirm']) || (string) $_POST['gdpr_confirm'] !== 'yes') {
        $errors[] = (string) ($contactErrors['gdpr'] ?? '');
    }

    [$uploadErrs, $attachments] = contact_process_photo_uploads($contactErrors);
    $errors = array_merge($errors, $uploadErrs);

    if ($errors !== []) {
        flash_set('contact', 'danger', implode(' ', $errors));
        header('Location: ' . aquamarine_url('contact.php') . '#form');
        exit;
    }

    $leadLang = aquamarine_locale();
    $ipHashed = hash('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    $pdoContact = aquamarine_pdo();
    if (! $pdoContact instanceof PDO) {
        error_log('Aquamarine: contact form submitted without DB connection');
        flash_set('contact', 'danger', (string) ($contactErrors['db_unavailable'] ?? ''));
        header('Location: ' . aquamarine_url('contact.php') . '#form');
        exit;
    }

    try {
        leads_insert(
            $pdoContact,
            $fullName,
            $phone,
            $email !== '' ? $email : null,
            $service !== '' ? $service : null,
            $preferredMag !== '' ? $preferredMag : null,
            $message !== '' ? $message : null,
            $attachments !== [] ? $attachments : null,
            $ipHashed,
            $leadLang
        );
    } catch (Throwable $e) {
        error_log('Aquamarine: lead insert failed: ' . $e->getMessage());
        flash_set('contact', 'danger', (string) ($contactErrors['db_unavailable'] ?? ''));
        header('Location: ' . aquamarine_url('contact.php') . '#form');
        exit;
    }

    contact_record_submit();

    $mailSent = false;
    if (($config['mail_enabled'] ?? false) === true) {
        $to = (string) ($config['contact_recipient_email'] ?? $config['email_contact']);
        $subjectRaw = str_replace(':site', (string) $config['site_name'], (string) ($c['mail_subject'] ?? '[:site] Cerere nouă site'));
        $subject = mb_encode_mimeheader($subjectRaw, 'UTF-8');
        if ($subject === false) {
            $subject = $subjectRaw;
        }

        $bodyLines = [
            'Nume: ' . $fullName,
            'Telefon: ' . $phone,
            'Email: ' . ($email ?: '—'),
            'Magazin preferat: ' . ($preferredMag !== '' ? $preferredMag : '—'),
            'Serviciu: ' . ($service ?: '—'),
            'Mesaj:',
            $message,
        ];
        if ($attachments !== []) {
            $bodyLines[] = 'Fișiere imagine (pe server, folder data):';
            foreach ($attachments as $rel) {
                $bodyLines[] = $rel;
            }
        }
        $body = implode("\r\n", $bodyLines);

        $fromLabel = mb_encode_mimeheader((string) $config['site_name'], 'UTF-8');
        $fromMailbox = filter_var((string) $config['email_contact'], FILTER_VALIDATE_EMAIL) ?: 'noreply@aquamarine.md';
        if ($fromLabel === false || $fromLabel === '') {
            $fromLabel = $config['site_name'];
        }

        $reply = $email !== '' ? $email : (string) $config['email_contact'];
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . $fromLabel . ' <' . $fromMailbox . '>',
            'Reply-To: ' . $reply,
            'X-Mail-Source: aquamarine-contact',
        ];
        $mailSent = @mail($to, $subject === false ? $subjectRaw : $subject, $body, implode("\r\n", $headers));
    }

    $flashMsgs = isset($c['flash']) && is_array($c['flash']) ? $c['flash'] : [];
    if (($config['mail_enabled'] ?? false) === true && $mailSent) {
        flash_set('contact', 'success', (string) ($flashMsgs['mail_ok'] ?? ''));
    } else {
        flash_set('contact', 'success', (string) ($flashMsgs['saved'] ?? ''));
    }

    header('Location: ' . aquamarine_url('contact.php') . '#form');
    exit;
}

$navCurrent = 'contact';
$pageTitle = (string) $c['meta']['title'];
$pageDescription = (string) $c['meta']['description'];
$feedback = flash_pull('contact');

require __DIR__ . '/includes/header.php';

$locations = isset($config['locations']) && is_array($config['locations']) ? $config['locations'] : [];
$contactIntent = isset($_GET['intent']) ? strtolower(trim((string) $_GET['intent'])) : '';
$contactServicePreselect = $contactIntent === 'b2b' ? 'b2b' : '';

$serviceChoices = isset($c['services']) && is_array($c['services']) ? $c['services'] : [];
?>

<main id="continut">
    <div class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-12 pb-16 sm:px-6 sm:pb-20 lg:py-14 lg:pb-24">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700"><?= esc((string) $c['eyebrow']) ?></p>
            <h1 class="font-display mt-3 text-pretty text-4xl font-bold tracking-tight text-ink lg:text-5xl"><?= esc((string) $c['h1']) ?></h1>
            <p class="mt-4 max-w-3xl text-lg text-slate-600">
                <?= esc((string) $c['lead']) ?>
            </p>

            <div id="form" class="mt-10 rounded-[32px] border border-slate-200 bg-white p-6 shadow-soft sm:p-10 lg:p-12">
                <h2 class="font-display text-2xl font-semibold text-ink sm:text-3xl"><?= esc((string) $c['form_title']) ?></h2>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base">
                    <?= esc((string) $c['form_lead']) ?>
                </p>
                <?php if ($feedback['message']) { ?>
                    <?php
                    $tone = ($feedback['type'] ?? '') === 'success'
                        ? 'border-emerald-200 bg-emerald-50 text-emerald-950'
                        : 'border-red-200 bg-red-50 text-red-950';
                    ?>
                    <div class="<?= esc($tone) ?> mt-6 rounded-2xl border px-4 py-3 text-sm font-semibold" role="status">
                        <?= esc((string) $feedback['message']) ?>
                    </div>
                <?php } ?>
                <form method="post" enctype="multipart/form-data" class="mt-8 space-y-6 lg:grid lg:grid-cols-2 lg:gap-x-8 lg:gap-y-6 lg:space-y-0" autocomplete="off" novalidate>
                    <?= public_csrf_field() ?>
                    <div class="hidden" aria-hidden="true">
                        <label for="company">Company URL</label>
                        <input type="text" id="company" name="company" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="lg:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700" for="full_name"><?= esc((string) $c['name']) ?></label>
                        <input required name="full_name" id="full_name" maxlength="140"
                               class="mt-1.5 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-base text-slate-900 outline-none ring-brand-600 focus:ring"
                               placeholder="<?= esc((string) $c['name_ph']) ?>">
                    </div>

                    <div class="lg:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700" for="phone"><?= esc((string) $c['phone']) ?></label>
                        <input required name="phone" id="phone" inputmode="tel" maxlength="32"
                               placeholder="<?= esc((string) $c['phone_ph']) ?>"
                               class="mt-1.5 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-base text-slate-900 outline-none ring-brand-600 focus:ring">
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700" for="email"><?= esc((string) $c['email']) ?> <span class="font-normal text-slate-500"><?= esc((string) $c['email_opt']) ?></span></label>
                        <input type="email" name="email" id="email" maxlength="190" placeholder="<?= esc((string) $c['email_ph']) ?>"
                               class="mt-1.5 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-base text-slate-900 outline-none ring-brand-600 focus:ring">
                    </div>

                    <?php if ($locations !== []) { ?>
                        <div class="lg:col-span-1">
                            <label class="block text-sm font-semibold text-slate-700" for="preferred_mag"><?= esc((string) $c['store']) ?></label>
                            <select id="preferred_mag" name="preferred_mag" class="mt-1.5 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-base text-slate-900 outline-none ring-brand-600 focus:ring">
                                <option value=""><?= esc((string) $c['store_none']) ?></option>
                                <?php foreach ($locations as $loc) {
                                    if (! is_array($loc)) {
                                        continue;
                                    }
                                    $optCity = isset($loc['city']) ? (string) $loc['city'] : '';
                                    if ($optCity === '') {
                                        continue;
                                    }
                                    ?>
                                    <option value="<?= esc($optCity) ?>"><?= esc(aquamarine_city_label($optCity)) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>

                    <div class="<?= $locations !== [] ? 'lg:col-span-1' : 'lg:col-span-2' ?>">
                        <label class="block text-sm font-semibold text-slate-700" for="service"><?= esc((string) $c['service']) ?></label>
                        <select id="service" name="service" class="mt-1.5 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-base font-medium text-slate-900 outline-none ring-brand-600 focus:ring">
                            <?php foreach ($serviceChoices as $val => $label) {
                                $isSelected = $contactServicePreselect !== ''
                                    ? $val === $contactServicePreselect
                                    : $val === '';
                                ?>
                                <option value="<?= esc((string) $val) ?>"<?= $isSelected ? ' selected' : '' ?>><?= esc((string) $label) ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700" for="message"><?= esc((string) $c['message']) ?></label>
                        <textarea id="message" name="message" rows="6" maxlength="4000" required
                                  class="mt-1.5 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-base leading-relaxed text-slate-900 outline-none ring-brand-600 focus:ring"
                                  placeholder="<?= esc((string) $c['message_ph']) ?>"></textarea>
                    </div>

                    <div class="lg:col-span-2" data-photo-upload-root>
                        <label class="block text-sm font-semibold text-slate-700" for="photos"><?= esc((string) $c['photos']) ?> <span class="font-normal text-slate-500"><?= esc((string) $c['photos_opt']) ?></span></label>
                        <div class="mt-1.5 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
                            <input type="file" name="photos[]" id="photos" accept="image/jpeg,image/png,image/webp" multiple
                                   class="sr-only"
                                   data-photo-input
                                   aria-describedby="photos-status">
                            <button type="button" data-photo-trigger
                                    class="inline-flex w-fit rounded-2xl border border-brand-200 bg-brand-50 px-4 py-2.5 text-sm font-semibold text-brand-900 hover:bg-brand-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                                <?= esc((string) $c['photos_btn']) ?>
                            </button>
                            <p id="photos-status" data-photo-status class="text-sm text-slate-600"><?= esc((string) $c['photos_none']) ?></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 lg:col-span-2">
                        <input type="checkbox" name="gdpr_confirm" id="gdpr_confirm" value="yes" required class="mt-1 rounded border-brand-300 text-brand-700 focus:ring-brand-700">
                        <label for="gdpr_confirm" class="text-xs leading-snug text-slate-700 sm:text-sm">
                            <?= str_replace('href="politica-confidentialitate.php"', 'href="' . esc(aquamarine_url('politica-confidentialitate.php')) . '"', (string) $c['gdpr']) ?>
                        </label>
                    </div>

                    <div class="lg:col-span-2">
                        <button type="submit"
                                class="w-full rounded-2xl bg-brand-700 px-5 py-4 text-base font-semibold text-white shadow-soft hover:bg-brand-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-700 sm:text-lg">
                            <?= esc((string) $c['submit']) ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
