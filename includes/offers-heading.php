<?php

declare(strict_types=1);

/**
 * @return array{0: string, 1: string}
 */
function offers_default_heading_lines(): array
{
    $offers = lang_data('home')['offers'] ?? [];
    if (! is_array($offers)) {
        $offers = [];
    }

    return [
        trim((string) ($offers['heading_line1'] ?? '')),
        trim((string) ($offers['heading_line2'] ?? '')),
    ];
}

/**
 * @return array{0: string, 1: string}
 */
function offers_heading_lines_from_string(string $heading): array
{
    [$d1, $d2] = offers_default_heading_lines();
    $heading = trim($heading);

    if ($heading !== '' && ! str_contains($heading, '|')) {
        return [$d1 !== '' ? $d1 : $heading, $d2];
    }

    return aquamarine_heading_lines($heading, $d1, $d2);
}

function offers_heading_attr_lines(string $heading): string
{
    [$line1, $line2] = offers_heading_lines_from_string($heading);

    return ' data-slide-heading-line1="' . esc($line1) . '"'
        . ' data-slide-heading-line2="' . esc($line2) . '"';
}
