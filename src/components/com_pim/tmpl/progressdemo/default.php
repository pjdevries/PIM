<?php
/**
 * @package     PIM
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */
// Path: components/com_pim/views/pim/tmpl/default.php

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('bootstrap.framework');

$wa = $this->document->getWebAssetManager();
$wa->usePreset('com_pim.progressDemo');
?>
<body>
<div class="container mt-5">
    <h2>Progress Bar</h2>
    <div class="progress-bar-wrapper">
        <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <button id="show-progress" type="button" class="btn btn-primary mt-2">Show progress</button>
</div>