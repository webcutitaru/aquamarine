<?php

declare(strict_types=1);

/**
 * Randare pagină filială. Setați înainte: $filialaCity, $filialaPageFile, $pageTitle, $pageDescription.
 *
 * @var array<string, mixed> $config
 * @var string $filialaCity
 * @var string $filialaPageFile
 * @var string $pageTitle
 * @var string $pageDescription
 */

$fil = lang_data('filial');
$cityLabel = aquamarine_city_label($filialaCity);

$filLoc = aquamarine_location_by_city($config, $filialaCity);
$filialaAddress = $filLoc !== null ? aquamarine_location_address($filLoc) : '';
$mapsUrl = $filLoc !== null ? trim((string) ($filLoc['maps_url'] ?? '')) : '';
if ($mapsUrl === '') {
    $mapsQuery = $filialaCity . ', ' . $filialaAddress . ', ' . t('country.md');
    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($mapsQuery);
}
$phoneDisp = $filLoc !== null ? trim((string) ($filLoc['phone_display'] ?? '')) : '';
$phoneE164 = $filLoc !== null ? trim((string) ($filLoc['phone_e164'] ?? '')) : '';
if ($phoneDisp === '' || $phoneE164 === '') {
    $phoneDisp = (string) ($config['phone_display'] ?? '');
    $phoneE164 = (string) ($config['phone_e164'] ?? '');
}
$hoursSpec = aquamarine_opening_hours_for_location($filLoc, $config);
$hourRows = aquamarine_opening_hours_ui_rows($hoursSpec);

$prodBase = aquamarine_production_base_url($config);
$absLogo = $prodBase . '/assets/images/aquamarine_logo_inline.png';
$filialPath = aquamarine_locale() === 'ru' ? '/ru/' . $filialaPageFile : '/' . $filialaPageFile;
$pageUrl = $prodBase . $filialPath;

$schemaDesc = str_replace(':city', $cityLabel, (string) $fil['schema_description']);

$schema = [
    '@context' => 'https://schema.org',
    '@type' => ['LocalBusiness', 'DryCleaningOrLaundry'],
    '@id' => $pageUrl . '#localbusiness',
    'name' => 'Aquamarine — ' . $cityLabel,
    'url' => $pageUrl,
    'image' => $absLogo,
    'telephone' => $phoneE164,
    'description' => $schemaDesc,
    'inLanguage' => aquamarine_locale(),
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => $filialaAddress,
        'addressLocality' => $filialaCity,
        'addressCountry' => 'MD',
    ],
    'openingHoursSpecification' => $hoursSpec,
];

$sameAsFil = [];
if (! empty((string) ($config['facebook_url'] ?? ''))) {
    $sameAsFil[] = (string) $config['facebook_url'];
}
if (! empty((string) ($config['instagram_url'] ?? ''))) {
    $sameAsFil[] = (string) $config['instagram_url'];
}
if ($sameAsFil !== []) {
    $schema['sameAs'] = $sameAsFil;
}

$extraHead = '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';

$leadText = str_replace(':city', $cityLabel, (string) $fil['lead_template']);
$h1Text = str_replace(':city', $cityLabel, (string) $fil['h1_template']);

require __DIR__ . '/header.php';
?>

<main id="continut">
    <div class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:py-16">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700"><a href="<?= esc(aquamarine_url('filiale.php')) ?>" class="rounded hover:text-brand-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600"><?= esc(t('filiale.all_branches')) ?></a></p>
            <h1 class="font-display mt-3 text-pretty text-4xl font-bold tracking-tight text-ink lg:text-5xl">
                <?= esc($h1Text) ?>
            </h1>
            <p class="mt-6 max-w-3xl text-pretty text-lg leading-relaxed text-slate-600">
                <?= esc($leadText) ?>
            </p>
            <div class="mt-8 grid gap-6 lg:grid-cols-2 lg:items-stretch">
                <div class="flex flex-col rounded-2xl border border-slate-200 bg-slate-50 p-6 shadow-sm">
                    <p class="text-sm font-semibold text-ink"><?= esc((string) $fil['address_label']) ?></p>
                    <p class="mt-2 text-lg text-slate-800">
                        <a href="<?= esc($mapsUrl) ?>"
                           target="_blank" rel="noopener noreferrer"
                           class="font-medium text-brand-900 underline decoration-brand-200 decoration-1 underline-offset-2 hover:text-brand-800 hover:decoration-brand-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-50 rounded">
                            <?= esc($cityLabel) ?><?php if ($filialaAddress !== '') { ?>, <?= esc($filialaAddress) ?><?php } ?>
                        </a>
                    </p>
                    <div class="mt-auto flex flex-wrap gap-3 pt-6">
                        <a href="tel:<?= esc(rawurlencode($phoneE164)) ?>"
                           class="inline-flex rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-ink hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                            <?= esc((string) $fil['call']) ?>: <?= esc($phoneDisp) ?>
                        </a>
                        <a href="<?= esc(aquamarine_url('contact.php')) ?>"
                           class="inline-flex rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-ink hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                            <?= esc((string) $fil['contact_form']) ?>
                        </a>
                    </div>
                </div>
                <div class="flex flex-col rounded-2xl border border-brand-100 bg-brand-50/80 p-6 lg:min-h-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-brand-800"><?= esc(t('filiale.reception_hours')) ?></p>
                    <dl class="mt-4 space-y-2.5 text-sm text-slate-700">
                        <?php
                        $hourRowCount = count($hourRows);
                        foreach ($hourRows as $hi => $pair) {
                            $dt = $pair[0] ?? '';
                            $dd = $pair[1] ?? '';
                            if ($dt === '' || $dd === '') {
                                continue;
                            }
                            $rowBorder = $hi < $hourRowCount - 1 ? 'border-b border-brand-100/80 pb-2' : '';
                            ?>
                            <div class="flex flex-wrap justify-between gap-2 <?= $rowBorder ?>">
                                <dt class="font-medium text-ink"><?= esc($dt) ?></dt>
                                <dd class="tabular-nums"><?= esc($dd) ?></dd>
                            </div>
                        <?php } ?>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/footer.php';
