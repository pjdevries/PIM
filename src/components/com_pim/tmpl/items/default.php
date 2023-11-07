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

$apiState = ItemsApiController::getApiState();

$xDebug = Factory::getApplication()->getInput()->getCmd('XDEBUG_SESSION_START', '');
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

<script>
    (function () {
        class Api {
            async getItems(url, headers = {}) {
                const defaultHeaders = {
                    'Accept': 'application/vnd.api+json'
                };

                return this.ajax(url, {
                    method: 'GET',
                    headers: Object.assign(defaultHeaders, headers)
                });
            }

            async postItem(url, headers = {}, data = null) {
                const defaultHeaders = {
                    'Content-Type': 'application/vnd.api+jsonn',
                    'Accept': 'application/vnd.api+json'
                };

                return this.ajax(url, {
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: Object.assign(defaultHeaders, headers)
                });
            }

            async deleteItem(url, headers = {}, data = null) {
                const defaultHeaders = {
                    'Content-Type': 'application/vnd.api+jsonn',
                    'Accept': 'application/vnd.api+json'
                };

                url += '&' + Object.entries(data)
                    .map((element) => element[0] + '=' + encodeURIComponent(element[1]))
                    .join('&');

                return this.ajax(url, {
                    method: 'DELETE',
                    headers: Object.assign(defaultHeaders, headers)
                });
            }

            async ajax(url, options) {
                if (!url) {
                    return {};
                }

                try {
                    const response = await fetch(url, options);
                    const decodedResponse = await response.json();

                    if (!response.ok) {
                        Joomla.renderMessages({
                            'error': [
                                decodedResponse.hasOwnProperty('message')
                                    ? decodedResponse.message
                                    : response.status + ' - ' + decodedResponse.errors[0].title
                            ]
                        });

                        return null;
                    }

                    return decodedResponse;
                } catch (e) {
                    console.log(e.message);

                    return null;
                }
            }
        }

        class ControllerResponseHandlers {
            loadItems(collection, items) {
                items.forEach(item => {
                    collection.push({
                        id: item.id,
                        title: item.title,
                        state: item.state,
                    });
                });
            }

            addItem(collection, item) {
                collection.push({
                    id: item.id,
                    title: item.title,
                    state: item.state,
                });
            }

            delItem(collection, itemId) {
                const index = collection.findIndex(item => item.id === itemId);

                if (index === -1) {
                    return;
                }

                collection.splice(index, 1);
            }
        }

        const WebserviceService = {
            endpoint: 'https://j4.obix.local:8482/api/v1/pim/items',
            token: 'c2hhMjU2OjM0OmZiODNkNmJmZGFjMTc3NTJkMmQyZGJmZDRiZDczYzQ4ZTgxNzk0NzBmNTJkOGVmZWFhNTdiZTU4ZDMyMWViYmQ=',
            items: [],
            newItem: '',
            api: new Api(),
            init() {
                <?php if ($xDebug) : ?>
                this.endpoint += '?XDEBUG_SESSION_START=' + '<?= $xDebug ?>';
                <?php endif; ?>

                this.loadItems();
            },
            loadItems() {
                this.items = [];

                this.api.getItems(this.endpoint, {'X-Joomla-Token': this.token})
                    .then(response => {
                        response.data.forEach(item => {
                            this.items.push({
                                id: item.attributes.id,
                                title: item.attributes.title,
                                state: item.attributes.state,
                            });
                        })
                    });

            },
            addItem() {
                if (this.newItem.trim().length < 1) {
                    return;
                }

                this.api.postItem(this.endpoint, {'X-Joomla-Token': this.token}, {
                    title: this.newItem
                })
                    .then(response => {
                        this.items.push({
                            id: response.data.attributes.id,
                            title: response.data.attributes.title,
                            state: response.data.attributes.state,
                        })
                    });

                this.newItem = '';
            }
        };

        const ControllerService = {
            endpoint: 'https://j4.obix.local:8482/index.php?option=com_pim',
            token: '<?= base64_encode($apiState->enabled ? $apiState->key : '') ?>',
            items: [],
            newItem: '',
            api: new Api(),
            handlers: new ControllerResponseHandlers(),
            init() {
                <?php if ($xDebug) : ?>
                this.endpoint += '&XDEBUG_SESSION_START=' + '<?= $xDebug ?>';
                <?php endif; ?>
                this.endpoint += '&task=ItemsApi.';

                this.loadItems();
            },
            loadItems() {
                this.items = [];

                this.api.getItems(this.endpoint + 'getItems', {'Api-Authorization': this.token})
                    .then(response => this.handlers.loadItems(this.items, response.data));

            },
            addItem() {
                if (this.newItem.trim().length < 1) {
                    return;
                }

                this.api.postItem(this.endpoint + 'postItem', {'Api-Authorization': this.token}, {
                    title: this.newItem
                })
                    .then(response => this.handlers.addItem(this.items, response.data));

                this.newItem = '';
            },
            delItem(itemId) {
                if (this.items.length < 1) {
                    return;
                }

                this.api.deleteItem(this.endpoint + 'deleteItem', {'Api-Authorization': this.token}, {
                    id: itemId
                })
                    .then(response => this.handlers.delItem(this.items, itemId));
            }
        };

        document.addEventListener('alpine:init', () => {
            Alpine.data('itemservice', () => ControllerService);
        })
    })();
</script>