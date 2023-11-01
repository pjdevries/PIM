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

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$user = Factory::getApplication()->getIdentity();
$userId = $user->id;
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
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

$wa->registerAndUseScript('alpinejs', '//unpkg.com/alpinejs', [], ['defer' => true]);
?>

<form action="<?php
echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post"
      name="adminForm" id="adminForm">

    <div class="table-responsive" x-data="items()" x-show="items.length">
        <?php
        if ($canCreate) : ?>
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
            </template>
            </tbody>
        </table>
    </div>
    <?php
    echo HTMLHelper::_('form.token'); ?>
</form>

<script>
    function items() {
        return {
            endpoint: 'https://j4.obix.local:8482/api/v1/pim/items',
            token: 'c2hhMjU2OjM0OmZiODNkNmJmZGFjMTc3NTJkMmQyZGJmZDRiZDczYzQ4ZTgxNzk0NzBmNTJkOGVmZWFhNTdiZTU4ZDMyMWViYmQ=',
            items: <?= json_encode($this->items) ?>,
            newItem: '',
            addItem() {
                if (this.newItem.trim().length < 1) {
                    return;
                }

                this.postItem(this.endpoint, {
                    title: this.newItem
                }).then(response => {
                    console.debug(response);

                    this.items.push({
                        id: response.data.attributes.id,
                        title: response.data.attributes.title,
                        state: response.data.attributes.state,
                    })
                });

                this.newItem = '';
            },
            async postItem(url, data = null) {
                return this.fetch(url, {
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: {
                        'Content-Type': 'application/vnd.api+jsonn',
                        'Accept': 'application/vnd.api+json',
                        'X-Joomla-Token': this.token
                    }
                });
            },
            async fetch(url, options) {
                if (!url) {
                    return {};
                }

                try {
                    const response = await fetch(url, options);

                    if (!response.ok) {
                        return null;
                    }

                    return response.json();
                } catch (e) {
                    console.log(e.message);

                    return null;
                }
            }
        }
    }
</script>