<?php

declare(strict_types=1);

require __DIR__ . '/includes/init.php';

$h = lang_data('home');
$navCurrent = 'home';
$pageTitle = (string) $h['meta']['title'];
$pageDescription = (string) $h['meta']['description'];
$locations = isset($config['locations']) && is_array($config['locations']) ? $config['locations'] : [];
$listaServicii = isset($h['services']) && is_array($h['services']) ? $h['services'] : [];
$offers = isset($h['offers']) && is_array($h['offers']) ? $h['offers'] : [];
$hero = isset($h['hero']) && is_array($h['hero']) ? $h['hero'] : [];
$advBlock = isset($h['advantages']) && is_array($h['advantages']) ? $h['advantages'] : [];
$adv = isset($advBlock['items']) && is_array($advBlock['items']) ? $advBlock['items'] : [];
$b2bTeaser = isset($h['b2b_teaser']) && is_array($h['b2b_teaser']) ? $h['b2b_teaser'] : [];
$nearYou = isset($h['near_you']) && is_array($h['near_you']) ? $h['near_you'] : [];
$reviewsUi = isset($h['reviews']) && is_array($h['reviews']) ? $h['reviews'] : [];
$citiesBadge = aquamarine_cities_badge();

/** Banner principal — oferte / promoții (MySQL sau fallback implicit). */
$slidesOferte = offers_fetch_active_slides(aquamarine_pdo());
$firstOfferSlide = isset($slidesOferte[0]) && is_array($slidesOferte[0]) ? $slidesOferte[0] : [];
$offerOverlayEyebrow = (string) ($firstOfferSlide['eyebrow'] ?? offers_lang_overlay_defaults()['eyebrow']);
$offerOverlayHeadingRaw = (string) ($firstOfferSlide['heading'] ?? ($offers['heading'] ?? ''));
[$offerOverlayHeadingLine1, $offerOverlayHeadingLine2] = offers_heading_lines_from_string($offerOverlayHeadingRaw);
$offerOverlaySub = (string) ($firstOfferSlide['sub'] ?? ($offers['sub'] ?? ''));

/** Recenzii Google Maps — text original (română), sursă unică. */
$reviewsHome = aquamarine_home_reviews();

$reviewSlidesDesktop = array_chunk($reviewsHome, 2);
$reviewSlidesMobile = [];
foreach ($reviewsHome as $row) {
    $reviewSlidesMobile[] = [$row];
}

$prodBase = aquamarine_production_base_url($config);
$homeUrl = $prodBase . '/';
$schemaImage = $prodBase . '/assets/images/aquamarine_logo_inline.png';
$mainLoc = isset($config['locations'][0]) && is_array($config['locations'][0]) ? $config['locations'][0] : [];
$streetMain = isset($mainLoc['address']) ? (string) $mainLoc['address'] : 'str. Decebal 130/A, mag. Kaufland';
$cityMain = isset($mainLoc['city']) ? (string) $mainLoc['city'] : 'Bălți';

$sameAsRaw = [];
if (! empty((string) ($config['facebook_url'] ?? ''))) {
    $sameAsRaw[] = (string) $config['facebook_url'];
}
if (! empty((string) ($config['instagram_url'] ?? ''))) {
    $sameAsRaw[] = (string) $config['instagram_url'];
}

$mainPhoneE164 = trim((string) ($mainLoc['phone_e164'] ?? ''));
if ($mainPhoneE164 === '') {
    $mainPhoneE164 = (string) ($config['phone_e164'] ?? '');
}
$mainHoursSpec = $mainLoc !== [] ? aquamarine_opening_hours_for_location($mainLoc, $config) : aquamarine_opening_hours_spec($config);

$payload = [
    '@context' => 'https://schema.org',
    '@type' => ['LocalBusiness', 'DryCleaningOrLaundry'],
    '@id' => $homeUrl . '#localbusiness',
    'name' => 'Aquamarine',
    'url' => $homeUrl,
    'image' => $schemaImage,
    'telephone' => $mainPhoneE164,
    'description' => t('schema.home_description'),
    'inLanguage' => aquamarine_locale(),
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => $streetMain,
        'addressLocality' => $cityMain,
        'addressCountry' => 'MD',
    ],
    'openingHoursSpecification' => $mainHoursSpec,
];

$grVal = isset($config['google_maps_rating']) ? (float) $config['google_maps_rating'] : 0.0;
if ($grVal > 0) {
    $ar = [
        '@type' => 'AggregateRating',
        'ratingValue' => number_format($grVal, 1, '.', ''),
        'bestRating' => '5',
        'worstRating' => '1',
    ];
    $rc = $config['google_maps_review_count'] ?? null;
    if ($rc !== null && $rc !== '' && is_numeric($rc)) {
        $ar['reviewCount'] = (string) (int) $rc;
    }
    $payload['aggregateRating'] = $ar;
}

if (($config['email_contact'] ?? '') !== '') {
    $payload['email'] = $config['email_contact'];
}
if ($sameAsRaw !== []) {
    $payload['sameAs'] = $sameAsRaw;
}

$extraHead = '<script type="application/ld+json">' . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';

require __DIR__ . '/includes/header.php';
?>

<main id="continut">
    <?php if ($slidesOferte !== []) { ?>
        <section
            class="relative isolate overflow-hidden border-b border-slate-900/15 bg-slate-900"
            aria-labelledby="oferte-heading"
        >
            <div
                class="relative h-[min(78svh,34rem)] w-full sm:h-[min(82svh,40rem)] lg:h-[min(85svh,44rem)]"
                data-offers-carousel
                role="region"
                aria-roledescription="carusel"
                aria-label="<?= esc((string) ($offers['carousel_label'] ?? '')) ?>"
            >
                <div class="absolute inset-0 z-0">
                    <div class="h-full overflow-hidden">
                        <div
                            class="flex h-full motion-reduce:transition-none"
                            data-carousel-track
                            style="transform: translateX(0%)"
                        >
                            <?php foreach ($slidesOferte as $idx => $slide) {
                                if (! is_array($slide)) {
                                    continue;
                                }

                                $img = isset($slide['image']) ? (string) $slide['image'] : '';
                                $alt = isset($slide['alt']) ? (string) $slide['alt'] : '';
                                $href = isset($slide['href']) ? (string) $slide['href'] : '';
                                if ($href !== '' && ! str_starts_with($href, 'http')) {
                                    $href = aquamarine_url($href);
                                }
                                if ($img === '') {
                                    continue;
                                }

                                $isFirst = $idx === 0;
                                $slideEyebrow = (string) ($slide['eyebrow'] ?? '');
                                $slideHeading = (string) ($slide['heading'] ?? '');
                                $slideSub = (string) ($slide['sub'] ?? '');
                                [$slideHeadingLine1, $slideHeadingLine2] = offers_heading_lines_from_string($slideHeading);
                                ?>
                                <div
                                    class="relative h-full min-w-0 shrink-0 grow-0 basis-full"
                                    data-carousel-slide
                                    data-slide-eyebrow="<?= esc($slideEyebrow) ?>"
                                    data-slide-heading-line1="<?= esc($slideHeadingLine1) ?>"
                                    data-slide-heading-line2="<?= esc($slideHeadingLine2) ?>"
                                    data-slide-sub="<?= esc($slideSub) ?>"
                                    <?= $isFirst ? 'data-carousel-active' : '' ?>
                                >
                                    <?php if ($href !== '') { ?>
                                        <a
                                            href="<?= esc($href) ?>"
                                            class="relative block h-full w-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900"
                                            data-carousel-slide-link
                                        >
                                    <?php } ?>
                                    <img
                                        src="<?= esc($img) ?>"
                                        alt="<?= esc($alt) ?>"
                                        class="<?= $href !== '' ? 'absolute inset-0 ' : '' ?>h-full w-full object-cover object-center"
                                        width="1200"
                                        height="380"
                                        sizes="100vw"
                                        decoding="async"
                                        <?= $isFirst ? 'fetchpriority="high"' : 'loading="lazy" fetchpriority="low"' ?>
                                    >
                                    <?php if ($href !== '') { ?>
                                        </a>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <?php if (count($slidesOferte) > 1) { ?>
                    <div class="pointer-events-none absolute inset-y-8 left-0 right-0 z-30 flex items-center justify-between px-3 sm:inset-y-12 sm:px-4">
                        <button
                            type="button"
                            class="pointer-events-auto inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/30 bg-slate-900/40 text-white shadow-lg backdrop-blur-md hover:bg-slate-900/55 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-transparent sm:h-12 sm:w-12"
                            data-carousel-prev
                            aria-label="<?= esc((string) ($offers['prev'] ?? '')) ?>"
                        >
                            <span class="sr-only"><?= esc((string) ($offers['prev_sr'] ?? '')) ?></span>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
                            </svg>
                        </button>
                        <button
                            type="button"
                            class="pointer-events-auto inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/30 bg-slate-900/40 text-white shadow-lg backdrop-blur-md hover:bg-slate-900/55 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-transparent sm:h-12 sm:w-12"
                            data-carousel-next
                            aria-label="<?= esc((string) ($offers['next'] ?? '')) ?>"
                        >
                            <span class="sr-only"><?= esc((string) ($offers['next_sr'] ?? '')) ?></span>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                            </svg>
                        </button>
                    </div>
                <?php } ?>

                <div class="pointer-events-none absolute inset-0 z-10 bg-gradient-to-t from-slate-950/90 via-slate-950/25 to-transparent" aria-hidden="true"></div>

                <div class="pointer-events-none absolute inset-x-0 bottom-0 z-20 px-4 pb-6 pt-28 sm:px-6 lg:pb-10">
                    <div class="mx-auto flex max-w-6xl flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                        <div class="min-w-0 w-full max-w-none sm:max-w-xl">
                            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-brand-100/95 max-sm:tracking-[0.15em]" data-offers-eyebrow>
                                <?= esc($offerOverlayEyebrow) ?>
                            </p>
                            <h2 id="oferte-heading" class="font-display mt-2 font-bold tracking-tight text-white" data-offers-heading>
                                <span class="block max-w-full text-[clamp(0.8125rem,3.4vw,1.25rem)] leading-tight sm:whitespace-nowrap sm:text-2xl lg:text-4xl" data-offers-heading-line1><?= esc($offerOverlayHeadingLine1) ?></span>
                                <span class="mt-1 block max-w-full text-pretty text-base font-bold leading-snug text-white/95 sm:text-xl lg:text-2xl<?= $offerOverlayHeadingLine2 === '' ? ' hidden' : '' ?>" data-offers-heading-line2><?= esc($offerOverlayHeadingLine2) ?></span>
                            </h2>
                            <p class="mt-2 text-sm leading-relaxed text-white/85" data-offers-sub>
                                <?= esc($offerOverlaySub) ?>
                            </p>
                        </div>
                        <?php if (count($slidesOferte) > 1) { ?>
                            <div
                                class="pointer-events-auto flex shrink-0 justify-center sm:justify-end"
                                data-carousel-dots
                                role="tablist"
                                aria-label="<?= esc((string) ($offers['dots'] ?? '')) ?>"
                            ></div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </section>
    <?php } ?>

    <section class="relative overflow-hidden border-b border-slate-200 bg-gradient-to-b from-white via-white to-brand-50/40">
        <div class="mx-auto grid max-w-6xl gap-12 px-4 pb-14 pt-10 sm:px-6 lg:grid-cols-2 lg:items-center lg:pb-16 lg:pt-14">
            <div class="relative z-10">
                <span class="inline-flex items-center rounded-full border border-brand-100 bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-800">
                    <?= esc($citiesBadge) ?>
                </span>
                <h1 class="font-display mt-6 text-balance text-4xl font-bold tracking-tight text-ink sm:text-5xl">
                    <?= esc((string) ($hero['h1'] ?? '')) ?>
                </h1>
                <p class="mt-6 max-w-xl text-pretty text-lg leading-relaxed text-slate-600">
                    <?= esc((string) ($hero['lead'] ?? '')) ?>
                </p>
                <dl class="mt-8 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <dt class="text-xs uppercase tracking-wide text-slate-500"><?= esc((string) ($hero['stat_services'] ?? '')) ?></dt>
                        <dd class="mt-1 font-semibold text-ink"><?= esc((string) ($hero['stat_services_val'] ?? '')) ?></dd>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <dt class="text-xs uppercase tracking-wide text-slate-500"><?= esc((string) ($hero['stat_expert'] ?? '')) ?></dt>
                        <dd class="mt-1 font-semibold text-ink"><?= esc((string) ($hero['stat_expert_val'] ?? '')) ?></dd>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <dt class="text-xs uppercase tracking-wide text-slate-500"><?= esc((string) ($hero['stat_locations'] ?? '')) ?></dt>
                        <dd class="mt-1 font-semibold text-ink"><?= esc((string) ($hero['stat_locations_val'] ?? '')) ?></dd>
                    </div>
                </dl>
            </div>
            <div class="relative lg:mx-0 lg:justify-self-end">
                <div class="absolute -left-24 -top-24 h-64 w-64 rounded-full bg-brand-200/40 blur-3xl lg:-top-36"></div>
                <div class="absolute bottom-[-2rem] right-[-3rem] h-56 w-56 rounded-full bg-brand-400/25 blur-3xl"></div>
                <figure class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-soft">
                    <div class="grid gap-[1px] bg-slate-100 sm:grid-cols-2 sm:items-stretch">
                        <div class="flex min-h-[13rem] flex-col justify-center bg-gradient-to-br from-brand-700 to-brand-500 p-6 text-white sm:col-span-1 sm:h-full sm:min-h-0">
                            <p class="text-sm font-semibold opacity-95">Aquamarine</p>
                            <p class="mt-4 font-display text-3xl font-bold leading-snug tracking-tight"><?= esc((string) ($hero['card_title'] ?? '')) ?></p>
                            <div class="mt-6 rounded-2xl bg-white/10 p-4 text-xs leading-relaxed text-white/90 backdrop-blur">
                                <?= esc((string) ($hero['card_note'] ?? '')) ?>
                            </div>
                        </div>
                        <div class="bg-slate-50 p-8 sm:flex sm:h-full sm:min-h-0 sm:flex-col sm:justify-start">
                            <ul class="mt-4 space-y-2.5 text-sm text-slate-700">
                                <?php foreach ($listaServicii as $serv) { ?>
                                    <li class="flex gap-3">
                                        <span class="mt-[5px] h-5 w-[3px] shrink-0 rounded-full bg-brand-600"></span>
                                        <?= esc((string) $serv) ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </figure>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700"><?= esc((string) ($advBlock['eyebrow'] ?? '')) ?></p>
            <h2 class="font-display mt-2 text-pretty text-3xl font-bold tracking-tight text-ink lg:text-4xl"><?= esc((string) ($advBlock['title'] ?? '')) ?></h2>
            <p class="mt-3 text-slate-600">
                <?= esc((string) ($advBlock['lead'] ?? '')) ?>
            </p>
        </div>
        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php
            foreach ($adv as $textAdv) {
                ?>
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm leading-relaxed text-slate-700"><?= esc((string) $textAdv) ?></p>
                </article>
                <?php
            }
?>
        </div>

        <div class="mx-auto mt-16 max-w-5xl rounded-3xl border border-brand-100 bg-brand-50/60 p-10">
            <h2 class="font-display text-center text-2xl font-bold text-ink sm:text-3xl"><?= esc((string) ($advBlock['commitments_title'] ?? '')) ?></h2>
            <div class="mt-10 grid gap-8 md:grid-cols-3">
                <div>
                    <h3 class="font-display font-semibold text-brand-950"><?= esc((string) ($advBlock['commit_delicate_title'] ?? '')) ?></h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-700">
                        <?= esc((string) ($advBlock['commit_delicate'] ?? '')) ?>
                    </p>
                </div>
                <div>
                    <h3 class="font-display font-semibold text-brand-950"><?= esc((string) ($advBlock['commit_speed_title'] ?? '')) ?></h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-700">
                        <?= esc((string) ($advBlock['commit_speed'] ?? '')) ?>
                    </p>
                </div>
                <div>
                    <h3 class="font-display font-semibold text-brand-950"><?= esc((string) ($advBlock['commit_env_title'] ?? '')) ?></h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-700">
                        <?= esc((string) ($advBlock['commit_env'] ?? '')) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="mx-auto mt-16 max-w-3xl rounded-3xl border border-slate-200 bg-white p-10 shadow-soft">
            <h2 class="font-display text-2xl font-bold text-ink"><?= esc((string) ($advBlock['about_title'] ?? '')) ?></h2>
            <p class="mt-6 text-lg leading-relaxed text-slate-700">
                <?= esc((string) ($advBlock['about'] ?? '')) ?>
            </p>
        </div>
    </section>

    <section class="border-y border-brand-100 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 sm:py-12">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700"><?= esc((string) ($b2bTeaser['eyebrow'] ?? '')) ?></p>
                    <p class="mt-2 max-w-2xl text-slate-600">
                        <?= esc((string) ($b2bTeaser['text'] ?? '')) ?>
                    </p>
                </div>
                <a href="<?= esc(aquamarine_url('business.php')) ?>"
                   class="inline-flex shrink-0 items-center justify-center rounded-2xl bg-brand-700 px-5 py-3 text-sm font-semibold text-white shadow-soft hover:bg-brand-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                    <?= esc((string) ($b2bTeaser['cta'] ?? '')) ?>
                </a>
            </div>
        </div>
    </section>

    <section class="border-y border-brand-100 bg-gradient-to-r from-brand-50 via-white to-brand-50" id="contact-mini">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <h2 class="font-display text-3xl font-bold text-ink"><?= esc((string) ($nearYou['title'] ?? '')) ?></h2>
            <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-soft sm:p-8">
                <p class="font-display text-lg font-semibold text-ink">
                    <a href="<?= esc(aquamarine_url('filiale.php')) ?>" class="rounded hover:text-brand-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white"><?= esc((string) ($nearYou['locations_link'] ?? '')) ?></a>
                </p>
                <div class="mt-6">
                    <?php
                    $filialeCardsShowPageCta = false;
                    require __DIR__ . '/includes/filiale-location-cards.php';
                    ?>
                </div>
            </div>
            <?php $waDigits = preg_replace('/\D+/', '', (string) $config['whatsapp_digits']); ?>
            <div class="mt-8 grid gap-3 text-sm font-semibold sm:grid-cols-2">
                <a href="https://wa.me/<?= esc($waDigits) ?>?text=<?= rawurlencode((string) ($nearYou['wa_message'] ?? '')) ?>"
                   class="rounded-2xl bg-brand-700 px-5 py-3 text-center text-white hover:bg-brand-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-700"
                   target="_blank" rel="noopener noreferrer">
                    <?= esc((string) ($nearYou['whatsapp'] ?? 'WhatsApp')) ?>
                </a>
                <a href="<?= esc(aquamarine_url('contact.php')) ?>"
                   class="rounded-2xl border border-brand-900/10 bg-white px-5 py-3 text-center text-ink hover:bg-white/80 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                    <?= esc((string) ($nearYou['contact'] ?? '')) ?>
                </a>
            </div>
        </div>

        <div id="recenzii-google" class="mt-14 w-full scroll-mt-28 border-t border-brand-200/60 pt-12 sm:pt-14">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <?php
                $googleReviewsUrl = trim((string) ($config['google_business_reviews_url'] ?? ''));
                $googleRatingVal = isset($config['google_maps_rating']) ? (float) $config['google_maps_rating'] : 0.0;
                $googleRatingFormatted = $googleRatingVal > 0 ? number_format($googleRatingVal, 1, ',', '') : '';
                $googleReviewCount = isset($config['google_maps_review_count']) && is_numeric($config['google_maps_review_count'])
                    ? (int) $config['google_maps_review_count']
                    : 0;
                ?>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700"><?= esc((string) ($reviewsUi['eyebrow'] ?? '')) ?></p>
                <h2 class="font-display mt-2 text-pretty text-3xl font-bold tracking-tight text-ink lg:text-4xl"><?= esc((string) ($reviewsUi['title'] ?? '')) ?></h2>
                <?php
                $reviewsLanguageNote = trim((string) ($reviewsUi['language_note'] ?? ''));
                if ($reviewsLanguageNote !== '') {
                    ?>
                    <p class="mt-2 max-w-2xl text-sm text-slate-600"><?= esc($reviewsLanguageNote) ?></p>
                <?php } ?>

                <?php if ($googleReviewsUrl !== '' && $googleRatingFormatted !== '' && $googleReviewCount > 0) { ?>
                    <div class="mt-6 flex flex-col gap-4 rounded-2xl border border-slate-200/80 bg-slate-100/90 px-4 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:gap-6 sm:px-6 sm:py-3.5">
                        <div class="flex min-w-0 flex-wrap items-center gap-x-2 gap-y-2 sm:gap-x-3">
                            <span class="sr-only"><?= esc(str_replace([':rating', ':count'], [$googleRatingFormatted, (string) $googleReviewCount], (string) ($reviewsUi['google_rating_sr'] ?? ''))) ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-6 w-6 shrink-0 sm:h-7 sm:w-7" aria-hidden="true">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span class="text-sm font-bold text-ink sm:text-base"><?= esc((string) ($reviewsUi['excellent'] ?? '')) ?></span>
                            <span class="text-base leading-none text-amber-400 sm:text-lg" aria-hidden="true">★★★★★</span>
                            <span class="text-base font-bold tabular-nums text-ink sm:text-lg"><?= esc(str_replace([':rating', ':count'], [$googleRatingFormatted, (string) $googleReviewCount], (string) ($reviewsUi['rating_line'] ?? ''))) ?></span>
                        </div>
                        <a href="<?= esc($googleReviewsUrl) ?>"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex w-full shrink-0 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-center text-sm font-bold text-ink shadow-sm transition hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-100 sm:w-auto sm:px-5">
                            <?= esc((string) ($reviewsUi['write'] ?? '')) ?>
                        </a>
                    </div>
                <?php } ?>
                <div
                    class="mt-10 flex items-stretch gap-3 sm:items-center sm:gap-4 md:hidden"
                    data-reviews-carousel-root
                    data-reviews-layout="mobile"
                    role="region"
                    aria-roledescription="carusel"
                    aria-label="<?= esc((string) ($reviewsUi['carousel'] ?? '')) ?>"
                >
                    <?php if (count($reviewSlidesMobile) > 1) { ?>
                        <button
                            type="button"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center self-center rounded-full border border-slate-200 bg-white text-slate-800 shadow-md hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-transparent sm:h-11 sm:w-11"
                            data-reviews-prev
                            aria-label="<?= esc((string) ($reviewsUi['prev'] ?? '')) ?>"
                        >
                            <span class="sr-only"><?= esc((string) ($offers['prev_sr'] ?? '')) ?></span>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
                            </svg>
                        </button>
                    <?php } ?>
                    <div class="min-w-0 flex-1">
                        <div class="overflow-hidden py-2 sm:py-4">
                            <div
                                class="flex w-full min-w-0 motion-reduce:transition-none"
                                data-reviews-track
                                style="transform: translateX(0%)"
                            >
                            <?php foreach ($reviewSlidesMobile as $sIdx => $chunk) {
                                $slideFirst = $sIdx === 0;
                                $rev = $chunk[0];
                                ?>
                                <div class="min-w-0 shrink-0 grow-0 basis-full overflow-hidden px-0" data-reviews-slide <?= $slideFirst ? 'data-reviews-active' : '' ?>>
                                    <?php require __DIR__ . '/includes/review-card-home.php'; ?>
                                </div>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php if (count($reviewSlidesMobile) > 1) { ?>
                        <button
                            type="button"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center self-center rounded-full border border-slate-200 bg-white text-slate-800 shadow-md hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-transparent sm:h-11 sm:w-11"
                            data-reviews-next
                            aria-label="<?= esc((string) ($reviewsUi['next'] ?? '')) ?>"
                        >
                            <span class="sr-only"><?= esc((string) ($offers['next_sr'] ?? '')) ?></span>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                            </svg>
                        </button>
                    <?php } ?>
                </div>

                <div
                    class="mt-10 hidden items-stretch gap-3 sm:items-center sm:gap-4 md:flex"
                    data-reviews-carousel-root
                    data-reviews-layout="desktop"
                    role="region"
                    aria-roledescription="carusel"
                    aria-label="<?= esc((string) ($reviewsUi['carousel'] ?? '')) ?>"
                >
                    <?php if (count($reviewSlidesDesktop) > 1) { ?>
                        <button
                            type="button"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center self-center rounded-full border border-slate-200 bg-white text-slate-800 shadow-md hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-transparent sm:h-11 sm:w-11"
                            data-reviews-prev
                            aria-label="<?= esc((string) ($reviewsUi['prev_group'] ?? '')) ?>"
                        >
                            <span class="sr-only"><?= esc((string) ($offers['prev_sr'] ?? '')) ?></span>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
                            </svg>
                        </button>
                    <?php } ?>
                    <div class="min-w-0 flex-1">
                        <div class="overflow-hidden py-2 sm:py-4">
                            <div
                                class="flex w-full min-w-0 motion-reduce:transition-none"
                                data-reviews-track
                                style="transform: translateX(0%)"
                            >
                            <?php foreach ($reviewSlidesDesktop as $sIdx => $chunk) {
                                $slideFirst = $sIdx === 0;
                                ?>
                                <div class="min-w-0 shrink-0 grow-0 basis-full overflow-hidden px-0" data-reviews-slide <?= $slideFirst ? 'data-reviews-active' : '' ?>>
                                    <div class="grid min-w-0 w-full grid-cols-2 gap-6 sm:gap-8">
                                        <?php foreach ($chunk as $rev) {
                                            require __DIR__ . '/includes/review-card-home.php';
                                        } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php if (count($reviewSlidesDesktop) > 1) { ?>
                        <button
                            type="button"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center self-center rounded-full border border-slate-200 bg-white text-slate-800 shadow-md hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-transparent sm:h-11 sm:w-11"
                            data-reviews-next
                            aria-label="<?= esc((string) ($reviewsUi['next_group'] ?? '')) ?>"
                        >
                            <span class="sr-only"><?= esc((string) ($offers['next_sr'] ?? '')) ?></span>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                            </svg>
                        </button>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
