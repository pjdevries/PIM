<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Pim
 * @author     Pieter-Jan de Vries <pieter@obix.nl>
 * @copyright  2023 Pieter-Jan de Vries
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Pim\Component\Pim\Site\Controller\ItemsApiController;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$app = Factory::getApplication();
$doc = $app->getDocument();

$user = $app->getIdentity();
$userId = $user->id;
$canCreate = $user->authorise('core.create', 'com_pim') && file_exists(
        JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'itemform.xml'
    );
$canEdit = $user->authorise('core.edit', 'com_pim') && file_exists(
        JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'itemform.xml'
    );
$canCheckin = $user->authorise('core.manage', 'com_pim');
$canChange = $user->authorise('core.edit.state', 'com_pim');
$canDelete = $user->authorise('core.delete', 'com_pim');

// Import CSS
/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_pim.list');

$doc->addScriptOptions('ajax-auth', [
    'endpoint' => 'https://j4.obix.local:8482/api/v1/pim/items',
    'token' => 'c2hhMjU2OjM0OmZiODNkNmJmZGFjMTc3NTJkMmQyZGJmZDRiZDczYzQ4ZTgxNzk0NzBmNTJkOGVmZWFhNTdiZTU4ZDMyMWViYmQ='
]);
$wa->useScript('com_pim.webserviceAjax');

//$doc->addScriptOptions('ajax-auth', [
//    'endpoint' => 'https://j4.obix.local:8482/index.php?option=com_pim&task=ItemsApi.',
//    'token' => 'ZmI2M2VlMGEtNTYxYS00Y2UzLWJmM2YtMWFkYzJhYjNlYjE0'
//]);
//$wa->useScript('com_pim.controllerAjax');
?>

<form action="<?php
echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post"
      name="adminForm" id="adminForm">

    <div class="table-responsive" x-data="itemservice" x-show="items.length">
        <?php
        if ($canCreate && $canEdit) : ?>
            <input type="text" class="form-control" placeholder="Item title" x-model="newItem">

            <a href="#" class="btn btn-success btn-small mt-2" @click="addItem()">
                <i class="icon-plus"></i>
                <?php
                echo Text::_('COM_PIM_ADD_ITEM'); ?></a>
        <?php
        endif; ?>

        <table class="table table-striped mt-4" id="itemList">
            <thead>
            <tr>
                <th class=''>
                    <?php
                    echo Text::_('COM_PIM_ITEMS_ID'); ?>
                </th>

                <th>
                    <?php
                    echo Text::_('JPUBLISHED'); ?>
                </th>

                <th>
                    <?php
                    echo Text::_('COM_PIM_ITEMS_TITLE'); ?>
                </th>

                <?php
                if ($canDelete) : ?>
                    <th>
                        &nbsp;
                    </th>
                <?php
                endif; ?>
            </tr>
            </thead>

            <tfoot>
            <tr>
                <td colspan="<?php
                echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
                    <div class="pagination">
                        <?php
                        echo $this->pagination->getPagesLinks(); ?>
                    </div>
                </td>
            </tr>
            </tfoot>

            <tbody>
            <template x-for="(item, index) in items" :key="item.id">
                <tr :class="`row{index}`">
                    <td x-text="item.id"></td>
                    <td>
                        <a class="btn btn-micro disabled" href="#">
                            <i :class="item.state === 1 ? 'icon-publish' : 'icon-unpublish'"></i>
                        </a>
                    </td>
                    <td x-text="item.title"></td>
                    <?php
                    if ($canDelete) : ?>
                        <td>
                            <a class="btn btn-micro" href="#" @click="delItem(item.id)">
                                <i class="icon-trash"></i>
                            </a>
                        </td>
                    <?php
                    endif; ?>
            </template>
            </tbody>
        </table>
    </div>
    <?php
    echo HTMLHelper::_('form.token'); ?>
</form>