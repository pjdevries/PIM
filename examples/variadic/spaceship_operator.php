<?php
/**
 * @package     PIM
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */
printf("1 <=> 1: %d\n", 1 <=> 1);
printf("2 <=> 1: %d\n", 2 <=> 1);
printf("1 <=> 2: %d\n\n", 1 <=> 2);

$items1 = [
    'een',
    'twee',
    'drie',
    'vier',
    'vijf',
    'zes'
];
printf("Original:\n%s\n\n", print_r($items1, true));

usort($items1, function (string $item1, string $item2): int
{
    if ($item1 === $item2) {
        return 0;
    }

    return $item1 < $item2 ? -1 : 1;
});

printf("Legacy sorted:\n%s\n\n", print_r($items1, true));

$items2 = [
    'aap',
    'noot',
    'mies',
    'wim',
    'zus',
    'jet'
];

printf("Original:\n%s\n\n", print_r($items2, true));

usort($items2, fn(string $item1, string $item2) => $item1 <=> $item2);

printf("Spaceship sorted:\n%s\n\n", print_r($items2, true));
