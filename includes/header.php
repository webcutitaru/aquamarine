<?php

declare(strict_types=1);

/** @var array<string, mixed> $config */

/** Pagina curentă: home | servicii-preturi | business | faq | contact | filiale */
$navCurrent ??= '';
$extraHead ??= '';

$pageTitle ??= $config['site_name'];
$pageDescription ??= t('meta.default_description');

$wa = preg_replace('/\D+/', '', (string) $config['whatsapp_digits']);
$tel = rawurlencode((string) $config['phone_e164']);

/** @var array<string, array{label:string, key:string}> $links */
$links = [
    'index.php' => ['label' => t('nav.home'), 'key' => 'home'],
    'servicii-si-preturi.php' => ['label' => t('nav.servicii'), 'key' => 'servicii-preturi'],
    'business.php' => ['label' => t('nav.business'), 'key' => 'business'],
    'filiale.php' => ['label' => t('nav.filiale'), 'key' => 'filiale'],
    'faq.php' => ['label' => t('nav.faq'), 'key' => 'faq'],
    'contact.php' => ['label' => t('nav.contact'), 'key' => 'contact'],
];

$seoBase = aquamarine_seo_base_url($config);
$canonicalHref = $seoBase . aquamarine_canonical_path();
$hreflangRo = aquamarine_locale_url('ro', $config);
$hreflangRu = aquamarine_locale_url('ru', $config);
$currentLocale = aquamarine_locale();
$isStagingHost = aquamarine_is_staging($config);
?>
<!DOCTYPE html>
<html class="scroll-smooth" lang="<?= esc($currentLocale) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <?php if ($isStagingHost) { ?>
        <meta name="robots" content="noindex,nofollow">
    <?php } ?>
    <link rel="canonical" href="<?= esc($canonicalHref) ?>">
    <link rel="alternate" hreflang="ro" href="<?= esc($hreflangRo) ?>">
    <link rel="alternate" hreflang="ru" href="<?= esc($hreflangRu) ?>">
    <link rel="alternate" hreflang="x-default" href="<?= esc($hreflangRo) ?>">
    <title><?= esc($pageTitle === $config['site_name'] ? $pageTitle : $pageTitle . ' · Aquamarine') ?></title>
    <meta name="description" content="<?= esc($pageDescription) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ecfeff',
                            100: '#cffafe',
                            200: '#a5f3fc',
                            300: '#67e8f9',
                            400: '#22d3ee',
                            500: '#06b6d4',
                            600: '#0891b2',
                            700: '#0e7490',
                            800: '#155e75',
                            900: '#164e63'
                        },
                        ink: '#0b1220'
                    },
                    fontFamily: {
                        sans: ['DM Sans', 'system-ui', 'sans-serif'],
                        display: ['Outfit', 'system-ui', 'sans-serif']
                    },
                    boxShadow: {
                        soft: '0 18px 50px rgba(11,18,32,0.10)'
                    },
                    keyframes: {
                        iconNudge: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '35%': { transform: 'translateY(-3px)' },
                            '65%': { transform: 'translateY(1px)' }
                        }
                    },
                    animation: {
                        'icon-nudge': 'iconNudge 0.45s cubic-bezier(0.34, 1.45, 0.64, 1) both'
                    }
                }
            }
        };
    </script>
    <?php if (! empty($extraHead)) {
        echo $extraHead;
    } ?>
</head>
<body class="min-h-screen bg-slate-50 pb-[max(0.75rem,env(safe-area-inset-bottom))] text-slate-800 antialiased md:pb-0">
<a href="#continut" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-brand-700 focus:px-4 focus:py-2 focus:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-50">
    <?= esc(t('skip.content')) ?>
</a>

<header class="sticky top-0 z-50 border-b border-slate-200/70 bg-gradient-to-b from-brand-50/40 via-white/90 to-white/95 backdrop-blur">
    <div class="mx-auto flex w-full max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:flex-nowrap lg:items-center lg:gap-2 xl:gap-4">
        <a href="<?= esc(aquamarine_url('index.php')) ?>" class="flex shrink-0 items-center rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
            <img src="<?= esc(aquamarine_asset_url('images/aquamarine_logo_inline.png')) ?>" alt="Aquamarine" class="h-8 w-auto sm:h-9 lg:h-10" width="2492" height="411" decoding="async">
        </a>

        <nav class="hidden min-w-0 shrink lg:flex lg:flex-1 lg:items-center lg:justify-center lg:gap-0.5 xl:gap-1" aria-label="<?= esc(t('aria.main_nav')) ?>">
            <?php foreach ($links as $href => $meta) {
                $isActive = $navCurrent === $meta['key'];
                $cls = $isActive
                    ? 'whitespace-nowrap rounded-xl bg-brand-50 px-2 py-2 text-xs font-semibold text-brand-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white xl:px-3 xl:text-sm'
                    : 'whitespace-nowrap rounded-xl px-2 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white xl:px-3 xl:text-sm';
                ?>
                <a class="<?= $cls ?>" href="<?= esc(aquamarine_url($href)) ?>"><?= esc((string) $meta['label']) ?></a>
            <?php } ?>
        </nav>

        <div class="flex shrink-0 items-center justify-end gap-2 lg:gap-3">
            <div class="hidden items-center rounded-xl border border-slate-200 bg-white p-0.5 text-xs font-bold lg:flex" role="group" aria-label="<?= esc(t('lang.switch')) ?>">
                <a href="<?= esc($hreflangRo) ?>"
                   class="<?= $currentLocale === 'ro' ? 'rounded-lg bg-brand-700 px-2.5 py-1.5 text-white' : 'rounded-lg px-2.5 py-1.5 text-slate-600 hover:text-brand-800' ?> focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600"
                   hreflang="ro" lang="ro"><?= esc(t('lang.ro')) ?></a>
                <a href="<?= esc($hreflangRu) ?>"
                   class="<?= $currentLocale === 'ru' ? 'rounded-lg bg-brand-700 px-2.5 py-1.5 text-white' : 'rounded-lg px-2.5 py-1.5 text-slate-600 hover:text-brand-800' ?> focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600"
                   hreflang="ru" lang="ru"><?= esc(t('lang.ru')) ?></a>
            </div>
            <a href="tel:<?= esc($tel) ?>"
               class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-xl border border-brand-100 bg-brand-50 p-2.5 text-brand-800 transition-colors duration-200 hover:border-brand-200 hover:bg-brand-100 hover:shadow-sm motion-safe:hover:animate-icon-nudge focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
               title="<?= esc(t('call.title')) ?>">
                <span class="sr-only"><?= esc(t('call.sr', ['phone' => (string) $config['phone_display']])) ?></span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                </svg>
            </a>
            <a href="https://wa.me/<?= esc($wa) ?>"
               target="_blank" rel="noopener noreferrer"
               class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-xl bg-brand-700 p-2.5 text-white shadow-soft transition-colors duration-200 hover:bg-brand-600 hover:shadow-md motion-safe:hover:animate-icon-nudge focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-700"
               title="<?= esc(t('whatsapp.title')) ?>">
                <span class="sr-only"><?= esc(t('whatsapp.open')) ?></span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 shrink-0" aria-hidden="true">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.881 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
            </a>
            <button type="button" id="mobile-menu-toggle"
                    class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-xl border border-slate-200 bg-white p-2.5 text-slate-800 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white lg:hidden"
                    aria-controls="mobile-menu" aria-expanded="false" aria-label="<?= esc(t('menu.open')) ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="hidden border-t border-slate-200 bg-white lg:hidden">
        <div class="mx-auto max-w-6xl px-4 py-4 sm:px-6">
            <div class="flex flex-col gap-1">
                <?php foreach ($links as $href => $meta) {
                    $isActive = $navCurrent === $meta['key'];
                    $cls = $isActive
                        ? 'rounded-xl bg-brand-50 px-3 py-2 text-base font-semibold text-brand-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white'
                        : 'rounded-xl px-3 py-2 text-base font-semibold text-slate-700 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white';
                    ?>
                    <a class="<?= $cls ?>" href="<?= esc(aquamarine_url($href)) ?>"><?= esc((string) $meta['label']) ?></a>
                <?php } ?>
            </div>
            <div class="mt-4 flex justify-center gap-2">
                <a href="<?= esc($hreflangRo) ?>" class="<?= $currentLocale === 'ro' ? 'bg-brand-700 text-white' : 'border border-slate-200 text-slate-700' ?> rounded-lg px-3 py-1.5 text-sm font-bold" hreflang="ro">RO</a>
                <a href="<?= esc($hreflangRu) ?>" class="<?= $currentLocale === 'ru' ? 'bg-brand-700 text-white' : 'border border-slate-200 text-slate-700' ?> rounded-lg px-3 py-1.5 text-sm font-bold" hreflang="ru">RU</a>
            </div>
            <div class="mt-4 grid gap-2 sm:grid-cols-2">
                <a href="tel:<?= esc($tel) ?>" class="inline-flex justify-center rounded-xl border border-brand-100 bg-brand-50 px-4 py-3 text-sm font-semibold text-brand-900 transition-colors duration-200 hover:border-brand-200 hover:bg-brand-100 hover:shadow-sm motion-safe:hover:animate-icon-nudge focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                    <?= esc(t('menu.call', ['phone' => (string) $config['phone_display']])) ?>
                </a>
                <a href="https://wa.me/<?= esc($wa) ?>" target="_blank" rel="noopener noreferrer"
                   class="inline-flex justify-center rounded-xl bg-brand-700 px-4 py-3 text-sm font-semibold text-white shadow-soft transition-colors duration-200 hover:bg-brand-600 hover:shadow-md motion-safe:hover:animate-icon-nudge focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-700">
                    <?= esc(t('whatsapp.open')) ?>
                </a>
            </div>
        </div>
    </div>
</header>
