<?php

declare(strict_types=1);

function aquamarine_ru_require(string $relativeScript): void
{
    $_SERVER['AQUAMARINE_LOCALE'] = 'ru';
    require dirname(__DIR__) . '/' . ltrim($relativeScript, '/');
}
