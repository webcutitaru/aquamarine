<?php

declare(strict_types=1);

require __DIR__ . '/includes/init.php';

$legal = lang_data('legal');
$p = $legal['privacy'] ?? [];
$navCurrent = '';
$pageTitle = (string) ($p['meta_title'] ?? '');
$pageDescription = (string) ($p['meta_description'] ?? '');

$emailBlock = '';
if (($config['email_contact'] ?? '') !== '') {
    $emailBlock = str_replace(
        [':email'],
        [(string) $config['email_contact']],
        (string) ($p['email_block'] ?? '')
    );
}

require __DIR__ . '/includes/header.php';
?>

<main id="continut">
    <article class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:py-16">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700"><?= esc((string) ($p['eyebrow'] ?? '')) ?></p>
        <h1 class="font-display mt-3 text-pretty text-4xl font-bold tracking-tight text-ink">
            <?= esc((string) ($p['h1'] ?? '')) ?>
        </h1>

        <div class="mt-10 space-y-8 text-base leading-relaxed text-slate-700">
            <?php
            $sections = isset($p['sections']) && is_array($p['sections']) ? $p['sections'] : [];
            foreach ($sections as $section) {
                if (! is_array($section)) {
                    continue;
                }
                $html = str_replace(
                    [':site', ':address', ':phone_e164', ':phone', ':email_block'],
                    [
                        esc((string) $config['site_name']),
                        esc((string) $config['address_full']),
                        esc(rawurlencode((string) $config['phone_e164'])),
                        esc((string) $config['phone_display']),
                        $emailBlock,
                    ],
                    (string) ($section['html'] ?? '')
                );
                ?>
                <section>
                    <h2 class="font-display text-xl font-semibold text-ink"><?= esc((string) ($section['title'] ?? '')) ?></h2>
                    <?= $html ?>
                </section>
            <?php } ?>
        </div>
    </article>
</main>

<?php require __DIR__ . '/includes/footer.php';
