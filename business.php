<?php

declare(strict_types=1);

require __DIR__ . '/includes/init.php';

$b = lang_data('business');
$navCurrent = 'business';
$pageTitle = (string) $b['meta']['title'];
$pageDescription = (string) $b['meta']['description'];
$heroB = isset($b['hero']) && is_array($b['hero']) ? $b['hero'] : [];
$trustB = isset($b['trust']) && is_array($b['trust']) ? $b['trust'] : [];
$industriesB = isset($b['industries']) && is_array($b['industries']) ? $b['industries'] : [];
$capabilitiesB = isset($b['capabilities']) && is_array($b['capabilities']) ? $b['capabilities'] : [];
$pillarsB = isset($b['pillars']) && is_array($b['pillars']) ? $b['pillars'] : [];
$processB = isset($b['process']) && is_array($b['process']) ? $b['process'] : [];
$networkB = isset($b['network']) && is_array($b['network']) ? $b['network'] : [];
$faqB = isset($b['faq']) && is_array($b['faq']) ? $b['faq'] : [];
$ctaB = isset($b['cta']) && is_array($b['cta']) ? $b['cta'] : [];

$b2bDiscount = isset($config['b2b_employee_discount_percent']) && is_numeric($config['b2b_employee_discount_percent'])
    ? (int) $config['b2b_employee_discount_percent']
    : 0;
$b2bDeliveryNote = trim((string) ($config['b2b_delivery_note'] ?? ''));
if ($b2bDeliveryNote === '') {
    $b2bDeliveryNote = (string) ($b['delivery_default'] ?? '');
}

$tel = rawurlencode((string) $config['phone_e164']);
$wa = preg_replace('/\D+/', '', (string) $config['whatsapp_digits']);
$phoneDisplay = (string) $config['phone_display'];

$googleRatingVal = isset($config['google_maps_rating']) ? (float) $config['google_maps_rating'] : 0.0;
$googleRatingFormatted = $googleRatingVal > 0 ? number_format($googleRatingVal, 1, ',', '') : '';
$googleReviewCount = isset($config['google_maps_review_count']) && is_numeric($config['google_maps_review_count'])
    ? (int) $config['google_maps_review_count']
    : 0;
$googleReviewsUrl = trim((string) ($config['google_business_reviews_url'] ?? ''));

$industryClusters = isset($industriesB['clusters']) && is_array($industriesB['clusters']) ? $industriesB['clusters'] : [];
$partnerServices = isset($capabilitiesB['services']) && is_array($capabilitiesB['services']) ? $capabilitiesB['services'] : [];
$faqB2b = isset($faqB['items']) && is_array($faqB['items']) ? $faqB['items'] : [];

$locations = isset($config['locations']) && is_array($config['locations']) ? $config['locations'] : [];
$areaServed = [];
foreach ($locations as $loc) {
    if (! is_array($loc)) {
        continue;
    }
    $city = isset($loc['city']) ? trim((string) $loc['city']) : '';
    if ($city !== '') {
        $areaServed[] = [
            '@type' => 'City',
            'name' => $city,
            'addressCountry' => 'MD',
        ];
    }
}

$prodBase = aquamarine_production_base_url($config);
$pageUrl = $prodBase . aquamarine_url('business.php');

$aboutEntity = [
    '@type' => 'DryCleaningOrLaundry',
    'name' => 'Aquamarine',
    'telephone' => (string) ($config['phone_e164'] ?? ''),
];
if ($areaServed !== []) {
    $aboutEntity['areaServed'] = $areaServed;
}

$faqSchemaEntities = [];
foreach ($faqB2b as $item) {
    $faqSchemaEntities[] = [
        '@type' => 'Question',
        'name' => $item['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $item['a'],
        ],
    ];
}

$schemaGraph = [
    [
        '@type' => 'WebPage',
        '@id' => $pageUrl . '#webpage',
        'url' => $pageUrl,
        'name' => $pageTitle . ' — Aquamarine',
        'description' => $pageDescription,
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => 'Aquamarine',
            'url' => $prodBase . '/',
        ],
        'about' => $aboutEntity,
    ],
    [
        '@type' => 'FAQPage',
        '@id' => $pageUrl . '#faq',
        'url' => $pageUrl . '#faq-b2b',
        'mainEntity' => $faqSchemaEntities,
    ],
];

$extraHead = '<script type="application/ld+json">' . json_encode(
    ['@context' => 'https://schema.org', '@graph' => $schemaGraph],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
) . '</script>';

require __DIR__ . '/includes/header.php';
?>

<main id="continut">
    <section class="relative overflow-hidden border-b border-slate-200 bg-gradient-to-b from-white via-white to-brand-50/40">
        <div class="mx-auto grid max-w-6xl gap-12 px-4 pb-14 pt-10 sm:px-6 lg:grid-cols-2 lg:items-center lg:pb-16 lg:pt-14">
            <div class="relative z-10">
                <span class="inline-flex items-center rounded-full border border-brand-100 bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-800">
                    <?= esc((string) ($heroB['badge'] ?? '')) ?>
                </span>
                <h1 class="font-display mt-6 text-balance text-4xl font-bold tracking-tight text-ink sm:text-5xl">
                    <?= esc((string) ($heroB['h1'] ?? '')) ?>
                </h1>
                <p class="mt-6 max-w-xl text-pretty text-lg leading-relaxed text-slate-600">
                    <?= esc((string) ($heroB['lead'] ?? '')) ?>
                </p>
                <dl class="mt-8 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <dt class="text-xs uppercase tracking-wide text-slate-500"><?= esc((string) ($heroB['stat_network'] ?? '')) ?></dt>
                        <dd class="mt-1 font-semibold text-ink"><?= esc((string) ($heroB['stat_network_val'] ?? '')) ?></dd>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <dt class="text-xs uppercase tracking-wide text-slate-500"><?= esc((string) ($heroB['stat_expert'] ?? '')) ?></dt>
                        <dd class="mt-1 font-semibold text-ink"><?= esc((string) ($heroB['stat_expert_val'] ?? '')) ?></dd>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <dt class="text-xs uppercase tracking-wide text-slate-500"><?= esc((string) ($heroB['stat_transparency'] ?? '')) ?></dt>
                        <dd class="mt-1 font-semibold text-ink"><?= esc((string) ($heroB['stat_transparency_val'] ?? '')) ?></dd>
                    </div>
                </dl>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="tel:<?= esc($tel) ?>"
                       class="inline-flex items-center justify-center rounded-2xl bg-brand-700 px-5 py-3 text-sm font-semibold text-white shadow-soft hover:bg-brand-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-700">
                        <?= esc((string) ($heroB['call'] ?? '')) ?> · <?= esc($phoneDisplay) ?>
                    </a>
                    <a href="https://wa.me/<?= esc($wa) ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center justify-center rounded-2xl border border-brand-200 bg-brand-50 px-5 py-3 text-sm font-semibold text-brand-900 hover:bg-brand-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                        WhatsApp
                    </a>
                    <a href="<?= esc(aquamarine_url('contact.php') . '?intent=b2b#form') ?>"
                       class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                        <?= esc((string) ($heroB['online'] ?? '')) ?>
                    </a>
                </div>
            </div>
            <div class="relative lg:mx-0 lg:justify-self-end">
                <div class="absolute -left-24 -top-24 h-64 w-64 rounded-full bg-brand-200/40 blur-3xl lg:-top-36" aria-hidden="true"></div>
                <figure class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-soft">
                    <div class="bg-gradient-to-br from-brand-700 to-brand-500 p-8 text-white">
                        <p class="text-sm font-semibold opacity-95"><?= esc((string) ($heroB['card_badge'] ?? '')) ?></p>
                        <p class="font-display mt-4 text-2xl font-bold leading-snug tracking-tight"><?= esc((string) ($heroB['card_title'] ?? '')) ?></p>
                        <ul class="mt-6 space-y-3 text-sm leading-relaxed text-white/90">
                            <?php foreach (($heroB['card_items'] ?? []) as $cardLine) { ?>
                            <li class="flex gap-3">
                                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-200" aria-hidden="true"></span>
                                <?= esc((string) $cardLine) ?>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </figure>
            </div>
        </div>
    </section>

    <?php if ($googleRatingFormatted !== '' && $googleReviewCount > 0) { ?>
        <section class="border-y border-brand-100 bg-brand-50/40" aria-label="<?= esc((string) ($trustB['aria'] ?? '')) ?>">
            <div class="mx-auto flex max-w-6xl flex-col items-start gap-4 px-4 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p class="max-w-2xl text-sm leading-relaxed text-slate-700 sm:text-base">
                    <?= esc(str_replace([':rating', ':count'], [$googleRatingFormatted, (string) $googleReviewCount], (string) ($trustB['line'] ?? ''))) ?>
                </p>
                <?php if ($googleReviewsUrl !== '') { ?>
                    <a href="<?= esc($googleReviewsUrl) ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="shrink-0 text-sm font-semibold text-brand-700 underline decoration-brand-300 underline-offset-2 hover:text-brand-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2">
                        <?= esc((string) ($trustB['link'] ?? '')) ?>
                    </a>
                <?php } ?>
            </div>
        </section>
    <?php } ?>

    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6" aria-labelledby="industrii-heading">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700"><?= esc((string) ($industriesB['eyebrow'] ?? '')) ?></p>
            <h2 id="industrii-heading" class="font-display mt-2 text-pretty text-3xl font-bold tracking-tight text-ink lg:text-4xl">
                <?= esc((string) ($industriesB['title'] ?? '')) ?>
            </h2>
            <p class="mt-3 text-slate-600">
                <?= esc((string) ($industriesB['lead'] ?? '')) ?>
            </p>
        </div>
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($industryClusters as $cluster) { ?>
                <article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="font-display text-lg font-semibold text-ink"><?= esc((string) $cluster['title']) ?></h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600"><?= esc((string) $cluster['lead']) ?></p>
                    <ul class="mt-auto space-y-1.5 border-t border-slate-100 pt-4 text-sm text-slate-700">
                        <?php foreach ($cluster['examples'] as $ex) { ?>
                            <li class="flex gap-2">
                                <span class="text-brand-600" aria-hidden="true">·</span>
                                <?= esc((string) $ex) ?>
                            </li>
                        <?php } ?>
                    </ul>
                </article>
            <?php } ?>
        </div>
    </section>

    <section class="border-y border-slate-200 bg-slate-50/80" aria-labelledby="servicii-parteneri-heading">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700"><?= esc((string) ($capabilitiesB['eyebrow'] ?? '')) ?></p>
                <h2 id="servicii-parteneri-heading" class="font-display mt-2 text-pretty text-3xl font-bold tracking-tight text-ink lg:text-4xl">
                    <?= esc((string) ($capabilitiesB['title'] ?? '')) ?>
                </h2>
            </div>
            <div class="mt-10 grid gap-4 sm:grid-cols-2">
                <?php foreach ($partnerServices as $svc) { ?>
                    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="font-display font-semibold text-ink"><?= esc((string) $svc['title']) ?></h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600"><?= esc((string) $svc['text']) ?></p>
                    </article>
                <?php } ?>
            </div>
            <p class="mt-8 text-sm text-slate-600">
                <?= esc((string) ($capabilitiesB['prices_link'] ?? '')) ?>
                <a class="font-semibold text-brand-700 underline decoration-brand-300 underline-offset-2 hover:text-brand-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2" href="<?= esc(aquamarine_url('servicii-si-preturi.php')) ?>"><?= esc((string) ($capabilitiesB['prices_anchor'] ?? '')) ?></a>.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6" aria-labelledby="piloni-heading">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700"><?= esc((string) ($pillarsB['eyebrow'] ?? '')) ?></p>
            <h2 id="piloni-heading" class="font-display mt-2 text-pretty text-3xl font-bold tracking-tight text-ink lg:text-4xl">
                <?= esc((string) ($pillarsB['title'] ?? '')) ?>
            </h2>
        </div>
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="font-display font-semibold text-brand-950"><?= esc((string) ($pillarsB['volume_title'] ?? '')) ?></h3>
                <p class="mt-3 text-sm leading-relaxed text-slate-700">
                    <?= esc((string) ($pillarsB['volume'] ?? '')) ?>
                </p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="font-display font-semibold text-brand-950"><?= esc((string) ($pillarsB['logistics_title'] ?? '')) ?></h3>
                <p class="mt-3 text-sm leading-relaxed text-slate-700">
                    <?= esc($b2bDeliveryNote) ?>
                    <?= esc((string) ($pillarsB['logistics_suffix'] ?? '')) ?>
                </p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="font-display font-semibold text-brand-950"><?= esc((string) ($pillarsB['benefits_title'] ?? '')) ?></h3>
                <p class="mt-3 text-sm leading-relaxed text-slate-700">
                    <?php if ($b2bDiscount > 0) { ?>
                        <?= esc(str_replace(':percent', (string) $b2bDiscount, (string) ($pillarsB['benefits_discount'] ?? ''))) ?>
                    <?php } else { ?>
                        <?= esc((string) ($pillarsB['benefits_default'] ?? '')) ?>
                    <?php } ?>
                </p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="font-display font-semibold text-brand-950"><?= esc((string) ($pillarsB['quality_title'] ?? '')) ?></h3>
                <p class="mt-3 text-sm leading-relaxed text-slate-700">
                    <?= esc((string) ($pillarsB['quality'] ?? '')) ?>
                </p>
            </article>
        </div>
    </section>

    <section class="border-y border-brand-100 bg-gradient-to-r from-brand-50 via-white to-brand-50" aria-labelledby="proces-heading">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <h2 id="proces-heading" class="font-display text-center text-2xl font-bold text-ink sm:text-3xl"><?= esc((string) ($processB['title'] ?? '')) ?></h2>
            <ol class="mt-12 grid gap-8 md:grid-cols-4">
                <?php
                $steps = isset($processB['steps']) && is_array($processB['steps']) ? $processB['steps'] : [];
                foreach ($steps as $si => $step) {
                    if (! is_array($step)) {
                        continue;
                    }
                    $num = $si + 1;
                    ?>
                <li class="<?= $si === 0 ? 'relative ' : '' ?>text-center md:text-left">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-brand-700 text-sm font-bold text-white"><?= esc((string) $num) ?></span>
                    <h3 class="font-display mt-4 font-semibold text-ink"><?= esc((string) ($step['title'] ?? '')) ?></h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                        <?= esc((string) ($step['text'] ?? '')) ?>
                    </p>
                </li>
                <?php } ?>
            </ol>
        </div>
    </section>

    <section class="border-b border-slate-200 bg-white" aria-labelledby="retea-heading">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700"><?= esc((string) ($networkB['eyebrow'] ?? '')) ?></p>
                <h2 id="retea-heading" class="font-display mt-2 text-pretty text-3xl font-bold tracking-tight text-ink lg:text-4xl">
                    <?= esc((string) ($networkB['title'] ?? '')) ?>
                </h2>
                <p class="mt-3 text-slate-600">
                    <?= esc((string) ($networkB['lead'] ?? '')) ?>
                </p>
            </div>
            <div class="mt-10">
                <?php
                $filialeCardsShowPageCta = true;
                require __DIR__ . '/includes/filiale-location-cards.php';
                ?>
            </div>
        </div>
    </section>

    <section id="faq-b2b" class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:pb-8" aria-labelledby="faq-b2b-heading">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700"><?= esc((string) ($faqB['eyebrow'] ?? '')) ?></p>
            <h2 id="faq-b2b-heading" class="font-display mt-2 text-pretty text-3xl font-bold tracking-tight text-ink"><?= esc((string) ($faqB['title'] ?? '')) ?></h2>
        </div>
        <div class="mt-10 space-y-6">
            <?php foreach ($faqB2b as $faq) { ?>
                <details class="group rounded-3xl border border-slate-200 bg-white px-8 py-5 shadow-sm marker:font-semibold [&_summary::-webkit-details-marker]:hidden">
                    <summary class="cursor-pointer select-none font-display text-lg font-semibold text-ink">
                        <?= esc((string) $faq['q']) ?>
                    </summary>
                    <p class="mt-4 text-base leading-relaxed text-slate-700"><?= esc((string) $faq['a']) ?></p>
                </details>
            <?php } ?>
        </div>
    </section>

    <section id="solicitare-b2b" class="mx-auto max-w-6xl px-4 pb-16 sm:px-6">
        <div class="rounded-3xl border border-brand-100 bg-brand-50/60 p-8 text-center sm:p-12">
            <h2 class="font-display text-2xl font-bold text-ink sm:text-3xl"><?= esc((string) ($ctaB['title'] ?? '')) ?></h2>
            <p class="mx-auto mt-4 max-w-2xl text-slate-600">
                <?= esc((string) ($ctaB['lead'] ?? '')) ?>
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="tel:<?= esc($tel) ?>"
                   class="inline-flex items-center justify-center rounded-2xl bg-brand-700 px-6 py-3 text-sm font-semibold text-white shadow-soft hover:bg-brand-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-50">
                    <?= esc($phoneDisplay) ?>
                </a>
                <a href="<?= esc(aquamarine_url('contact.php') . '?intent=b2b#form') ?>"
                   class="inline-flex items-center justify-center rounded-2xl border border-brand-200 bg-white px-6 py-3 text-sm font-semibold text-brand-900 hover:bg-brand-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-brand-50">
                    <?= esc((string) ($ctaB['online'] ?? '')) ?>
                </a>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php';
