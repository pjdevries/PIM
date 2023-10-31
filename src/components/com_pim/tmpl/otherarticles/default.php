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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

// Import CSS
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_pim.admin')
    ->useScript('com_pim.admin');
?>

<form action="<?php
echo Route::_('index.php?option=com_pim&view=items'); ?>" method="post"
      name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">

                <div class="clearfix"></div>
                <table class="table table-striped" id="itemList">
                    <thead>
                    <th class='left'>
                        <?php
                        echo Text::_('COM_PIM_ITEMS_TITLE'); ?>
                    </th>
                    </thead>

                    <tbody>
                    <?php
                    foreach ($this->items as $i => $item) :
                        ?>
                        <tr class="row<?php
                        echo $i % 2; ?>" data-draggable-group='1' data-transition>
                            <td>
                                <?php
                                echo $this->escape($item->title); ?>
                            </td>
                        </tr>
                    <?php
                    endforeach; ?>
                    </tbody>
                </table>

                <?php
                echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>