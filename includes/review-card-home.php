<?php

declare(strict_types=1);

/** @var array{name:string, rating:int, when:string, text:string} $rev */

$name = isset($rev['name']) ? (string) $rev['name'] : '';
$rating = isset($rev['rating']) ? (int) $rev['rating'] : 5;
$when = isset($rev['when']) ? (string) $rev['when'] : '';
$text = isset($rev['text']) ? (string) $rev['text'] : '';
$rating = max(1, min(5, $rating));
?>
<article class="flex h-full min-h-[12rem] flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:min-h-0 sm:p-6">
    <div class="flex flex-wrap items-start justify-between gap-2">
        <p class="font-display text-base font-semibold text-ink"><?= esc($name) ?></p>
        <p class="text-xs font-medium uppercase tracking-wide text-slate-500"><?= esc($when) ?></p>
    </div>
    <div class="mt-3 flex gap-0.5 text-amber-500" role="img" aria-label="<?= esc(t('reviews.rating_aria', ['rating' => (string) $rating])) ?>">
        <?php for ($s = 1; $s <= 5; $s++) {
            $on = $s <= $rating;
            ?>
            <span class="text-lg leading-none <?= $on ? '' : 'text-slate-200' ?>" aria-hidden="true">★</span>
        <?php } ?>
    </div>
    <blockquote class="mt-4 flex-1 border-l-2 border-brand-100 pl-4">
        <p class="text-sm leading-relaxed text-slate-700 text-pretty"><?= esc($text) ?></p>
    </blockquote>
</article>
