<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   (C) 2016 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * https://css-tricks.com/drag-and-drop-file-uploading/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$app = Factory::getApplication();
$doc = $app->getDocument();

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $doc->getWebAssetManager();
if (!$wa->assetExists('preset', 'obix.upload')) {
    $wr = $wa->getRegistry();
    $wr->addRegistryFile('media/obix/joomla.asset.json');
}
$wa->usePreset('obix.upload');
?>

<div class="upload-box">
    <div class="upload-box__icon"><span class="fa fa-file-upload"></span></div>
    <div class="upload-box__input">
        <input class="upload-box__file" type="file" name="jform[files][]" id="jform_files" data-multiple-caption="{count} files selected" multiple />
        <label for="jform_files"><strong>Choose a file</strong><span class="upload-box__dragndrop"> or drag it here</span>.</label>
        <button class="upload-box__button" type="submit">Upload</button>
    </div>
    <div class="upload-box__uploading">Uploading…</div>
    <div class="upload-box__success">Done!</div>
    <div class="upload-box__error">Error! <span></span>.</div>
</div>
