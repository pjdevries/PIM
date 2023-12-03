<?php
/**
 * @package     ObixUploads
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

defined('_JEXEC') or die;

$value = $field->value;

if ($value == '') {
    return;
}

if (is_array($value)) {
    $value = implode(', ', $value);
}

echo htmlentities($value);
