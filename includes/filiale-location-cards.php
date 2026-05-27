<?php

declare(strict_types=1);

/**
 * Carduri magazine (home / filiale).
 *
 * @var list<array<string, mixed>> $locations
 * @var array<string, mixed> $config
 * @var bool $filialeCardsShowPageCta afișează butonul „Pagina filialei”
 */

if (! isset($filialeCardsShowPageCta)) {
    $filialeCardsShowPageCta = false;
}
?>
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <?php foreach ($locations as $loc) {
        if (! is_array($loc)) {
            continue;
        }
        $c = isset($loc['city']) ? (string) $loc['city'] : '';
        $a = aquamarine_location_address($loc);
        $page = isset($loc['page']) ? (string) $loc['page'] : '';
        $mapsUrl = trim((string) ($loc['maps_url'] ?? ''));
        if ($mapsUrl === '') {
            $mapsQuery = $a !== '' ? $c . ', ' . $a . ', Republica Moldova' : $c . ', Republica Moldova';
            $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($mapsQuery);
        }
        $phoneDisp = trim((string) ($loc['phone_display'] ?? ''));
        $phoneE164 = trim((string) ($loc['phone_e164'] ?? ''));
        if ($phoneDisp === '' || $phoneE164 === '') {
            $phoneDisp = (string) ($config['phone_display'] ?? '');
            $phoneE164 = (string) ($config['phone_e164'] ?? '');
        }
        if ($c === '' || $page === '') {
            continue;
        }
        $hoursSpec = aquamarine_opening_hours_for_location($loc, $config);
        $hourRows = aquamarine_opening_hours_ui_rows($hoursSpec);
        ?>
        <article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="font-display text-lg font-semibold text-ink">
                <a href="<?= esc(aquamarine_url($page)) ?>"
                   class="rounded hover:text-brand-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                    <?= esc(aquamarine_city_label($c)) ?>
                </a>
            </h3>
            <p class="mt-2 text-sm leading-relaxed text-slate-700">
                <a href="<?= esc($mapsUrl) ?>"
                   target="_blank" rel="noopener noreferrer"
                   class="font-medium text-brand-900 underline decoration-brand-200 decoration-1 underline-offset-2 hover:text-brand-800 hover:decoration-brand-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                    <?= esc(aquamarine_city_label($c)) ?><?php if ($a !== '') { ?>, <?= esc($a) ?><?php } ?>
                </a>
            </p>
            <p class="mt-3 text-sm text-slate-700">
                <a href="tel:<?= esc(rawurlencode($phoneE164)) ?>"
                   class="font-semibold text-ink hover:text-brand-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white rounded">
                    <?= esc($phoneDisp) ?>
                </a>
            </p>
            <?php if ($hourRows !== []) { ?>
                <div class="mt-4 border-t border-slate-100 pt-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-brand-800"><?= esc(t('filiale.reception_hours')) ?></p>
                    <dl class="mt-2 space-y-1.5 text-xs text-slate-600">
                        <?php foreach ($hourRows as $pair) {
                            $dt = $pair[0] ?? '';
                            $dd = $pair[1] ?? '';
                            if ($dt === '' || $dd === '') {
                                continue;
                            }
                            ?>
                            <div class="flex flex-wrap justify-between gap-x-2 gap-y-0.5">
                                <dt class="font-medium text-slate-700"><?= esc($dt) ?></dt>
                                <dd class="tabular-nums text-slate-800"><?= esc($dd) ?></dd>
                            </div>
                        <?php } ?>
                    </dl>
                </div>
            <?php } ?>
            <?php if ($filialeCardsShowPageCta) { ?>
                <div class="mt-4 border-t border-slate-100 pt-4">
                    <a href="<?= esc(aquamarine_url($page)) ?>"
                       class="inline-flex rounded-xl bg-brand-700 px-3 py-2 text-xs font-semibold text-white hover:bg-brand-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-700">
                        <?= esc(t('filiale.branch_page')) ?>
                    </a>
                </div>
            <?php } ?>
        </article>
    <?php } ?>
</div>
