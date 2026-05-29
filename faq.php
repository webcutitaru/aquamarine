<?php

declare(strict_types=1);

require __DIR__ . '/includes/init.php';

$f = lang_data('faq');
$navCurrent = 'faq';
$pageTitle = (string) $f['meta']['title'];
$pageDescription = (string) $f['meta']['description'];
$items = isset($f['items']) && is_array($f['items']) ? $f['items'] : [];
$extraHead = '';

$faqSchemaEntities = [];
foreach ($items as $item) {
    if (! is_array($item)) {
        continue;
    }
    $q = trim((string) ($item['q'] ?? ''));
    $a = trim((string) ($item['a'] ?? ''));
    if ($q === '' || $a === '') {
        continue;
    }
    $faqSchemaEntities[] = [
        '@type' => 'Question',
        'name' => $q,
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $a,
        ],
    ];
}

if ($faqSchemaEntities !== []) {
    $extraHead = '<script type="application/ld+json">'
        . json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqSchemaEntities,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        . '</script>';
}

require __DIR__ . '/includes/header.php';
?>

<main id="continut">
    <div class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
            <h1 class="font-display max-w-xl text-pretty text-4xl font-bold tracking-tight text-ink lg:text-[2.75rem]"><?= esc($pageTitle) ?></h1>
            <p class="mt-4 max-w-2xl text-slate-600"><?= esc((string) $f['intro']) ?></p>
        </div>
    </div>

    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:pb-16">
        <div class="space-y-6">
            <?php foreach ($items as $faq) {
                if (! is_array($faq)) {
                    continue;
                }
                ?>
                <details class="group rounded-3xl border border-slate-200 bg-white px-8 py-5 shadow-sm marker:font-semibold [&_summary::-webkit-details-marker]:hidden">
                    <summary class="cursor-pointer select-none font-display text-lg font-semibold text-ink">
                        <?= esc((string) ($faq['q'] ?? '')) ?>
                    </summary>
                    <p class="mt-4 text-base leading-relaxed text-slate-700"><?= esc((string) ($faq['a'] ?? '')) ?></p>
                </details>
            <?php } ?>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php';
