<?php

declare(strict_types=1);

$year = date('Y');
$locations = isset($config['locations']) && is_array($config['locations']) ? $config['locations'] : [];
?>
<footer class="mt-16 border-t border-slate-200 bg-white">
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:grid lg:grid-cols-3 lg:gap-10">
        <div>
            <img src="<?= esc(aquamarine_asset_url('images/aquamarine_logo_inline.png')) ?>" alt="Aquamarine" class="h-9 w-auto sm:h-10" width="2492" height="411" decoding="async">
            <p class="mt-4 max-w-md text-sm leading-relaxed text-slate-600">
                <?= esc(t('footer.blurb')) ?>
            </p>
        </div>

        <div class="mt-10 lg:mt-0">
            <p class="font-display text-base font-semibold text-ink"><?= esc(t('footer.quick_nav')) ?></p>
            <ul class="mt-4 space-y-2 text-sm text-slate-700">
                <li><a class="rounded-md hover:text-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white" href="<?= esc(aquamarine_url('servicii-si-preturi.php')) ?>"><?= esc(t('nav.servicii')) ?></a></li>
                <li><a class="rounded-md hover:text-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white" href="<?= esc(aquamarine_url('business.php')) ?>"><?= esc(t('nav.business')) ?></a></li>
                <li><a class="rounded-md hover:text-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white" href="<?= esc(aquamarine_url('filiale.php')) ?>"><?= esc(t('nav.filiale')) ?></a></li>
                <li><a class="rounded-md hover:text-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white" href="<?= esc(aquamarine_url('contact.php')) ?>"><?= esc(t('nav.contact')) ?></a></li>
            </ul>
        </div>

        <div class="mt-10 lg:mt-0">
            <p class="font-display text-base font-semibold text-ink"><?= esc(t('footer.contact')) ?></p>
            <ul class="mt-4 space-y-2 text-sm text-slate-700">
                <li><span class="text-slate-500"><?= esc(t('footer.phone')) ?></span>
                    <a class="rounded-md hover:text-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white" href="tel:<?= esc(rawurlencode((string) $config['phone_e164'])) ?>"><?= esc((string) $config['phone_display']) ?></a>
                </li>
                <?php if (($config['email_contact'] ?? '') !== '') { ?>
                    <li><span class="text-slate-500"><?= esc(t('footer.email')) ?></span>
                        <a class="rounded-md hover:text-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white" href="mailto:<?= esc((string) $config['email_contact']) ?>"><?= esc((string) $config['email_contact']) ?></a>
                    </li>
                <?php } ?>
                <li class="text-slate-700">
                    <span class="text-slate-500"><?= esc(t('footer.locations')) ?></span>
                    <?php
                    if ($locations === []) {
                        echo ' ' . esc(aquamarine_cities_badge());
                    } else {
                        $locFirst = true;
                        foreach ($locations as $loc) {
                            if (! is_array($loc)) {
                                continue;
                            }
                            $c = isset($loc['city']) ? (string) $loc['city'] : '';
                            $a = isset($loc['address']) ? (string) $loc['address'] : '';
                            if ($c === '') {
                                continue;
                            }
                            $mapsUrl = trim((string) ($loc['maps_url'] ?? ''));
                            if ($mapsUrl === '') {
                                $mapsQuery = $a !== '' ? $c . ', ' . $a . ', ' . t('country.md') : $c . ', ' . t('country.md');
                                $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($mapsQuery);
                            }
                            if (! $locFirst) {
                                ?><span class="text-slate-400"> · </span><?php
                            }
                            $locFirst = false;
                            ?>
                            <a href="<?= esc($mapsUrl) ?>"
                               target="_blank" rel="noopener noreferrer"
                               class="rounded-md font-medium text-slate-800 underline decoration-slate-300 decoration-1 underline-offset-2 hover:text-brand-700 hover:decoration-brand-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white"><?= esc(aquamarine_city_label($c)) ?></a>
                            <?php
                        }
                    }
                    ?>
                </li>
                <?php if (! empty((string) $config['google_maps_url'])) { ?>
                    <li>
                        <a class="rounded-md hover:text-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white" href="<?= esc((string) $config['google_maps_url']) ?>" target="_blank" rel="noopener noreferrer"><?= esc(t('footer.maps')) ?></a>
                    </li>
                <?php } ?>
            </ul>
            <?php
            $fb = isset($config['facebook_url']) ? (string) $config['facebook_url'] : '';
            $ig = isset($config['instagram_url']) ? (string) $config['instagram_url'] : '';
            if ($fb !== '' || $ig !== '') {
                ?>
                <p class="mt-5 text-sm font-semibold text-ink"><?= esc(t('footer.social')) ?></p>
                <p class="mt-2 flex flex-wrap gap-2">
                    <?php if ($fb !== '') { ?>
                        <a href="<?= esc($fb) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= esc(t('social.facebook')) ?>"
                           class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-brand-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    <?php } ?>
                    <?php if ($ig !== '') { ?>
                        <a href="<?= esc($ig) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= esc(t('social.instagram')) ?>"
                           class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-brand-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                <circle cx="12" cy="12" r="4"/>
                                <circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/>
                            </svg>
                        </a>
                    <?php } ?>
                </p>
                <?php
            }
?>
        </div>
    </div>

    <div class="border-t border-slate-100">
        <div class="mx-auto flex max-w-6xl flex-col gap-3 px-4 py-8 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <p><?= esc(t('footer.copyright', ['year' => $year])) ?></p>
            <div class="flex flex-wrap gap-x-4 gap-y-2 sm:justify-end">
                <a class="font-medium text-slate-600 underline decoration-slate-300 decoration-1 underline-offset-2 hover:text-brand-700 hover:decoration-brand-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white" href="<?= esc(aquamarine_url('politica-confidentialitate.php')) ?>"><?= esc(t('footer.privacy')) ?></a>
                <a class="font-medium text-slate-600 underline decoration-slate-300 decoration-1 underline-offset-2 hover:text-brand-700 hover:decoration-brand-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white" href="<?= esc(aquamarine_url('termeni.php')) ?>"><?= esc(t('footer.terms')) ?></a>
            </div>
        </div>
    </div>
</footer>

<script src="<?= esc(aquamarine_asset_url('js/main.js')) ?>" defer></script>
</body>
</html>
