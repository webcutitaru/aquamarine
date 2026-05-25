<?php

declare(strict_types=1);

require __DIR__ . '/includes/init.php';

$filialaCity = 'Drochia';
$filialaPageFile = 'curatatorie-profesionala-haine-drochia.php';
$navCurrent = 'filiale';
$fil = lang_data('filial');
$cityMeta = $fil['cities'][$filialaCity] ?? ['title' => '', 'description' => ''];
$pageTitle = (string) $cityMeta['title'];
$pageDescription = (string) $cityMeta['description'];

require __DIR__ . '/includes/render-filial.php';
