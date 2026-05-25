<?php

declare(strict_types=1);

require __DIR__ . '/includes/init.php';

$f = lang_data('filiale');
$navCurrent = 'filiale';
$pageTitle = (string) $f['meta']['title'];
$pageDescription = (string) $f['meta']['description'];

$extraHead = '';
require __DIR__ . '/includes/header.php';

$locations = isset($config['locations']) && is_array($config['locations']) ? $config['locations'] : [];
?>

<main id="continut">
    <div class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:py-16">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700"><?= esc((string) $f['eyebrow']) ?></p>
            <h1 class="font-display mt-3 text-pretty text-4xl font-bold tracking-tight text-ink lg:text-5xl"><?= esc((string) $f['h1']) ?></h1>
            <p class="mt-6 max-w-3xl text-lg text-slate-600">
                <?= esc((string) $f['lead']) ?>
            </p>
            <div class="mt-12">
                <?php
                $filialeCardsShowPageCta = true;
                require __DIR__ . '/includes/filiale-location-cards.php';
                ?>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php';
