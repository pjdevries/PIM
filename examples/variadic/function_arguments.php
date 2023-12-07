<?php

/**
 * @package     PIM
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */
function before(int $number1, int $number2)
{
    $args = func_get_args();
    print_r($args);
}

before(55, 16);

function now(...$numbers)
{
    print_r($numbers);
}

now(55, 16);