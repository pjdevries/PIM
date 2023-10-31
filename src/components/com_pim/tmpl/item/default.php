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

$canEdit = Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_pim.' . $this->item->id);

if (!$canEdit && Factory::getApplication()->getIdentity()->authorise('core.edit.own', 'com_pim' . $this->item->id)) {
    $canEdit = Factory::getApplication()->getIdentity()->id == $this->item->created_by;
}
?>

    <div class="item_fields">

        <table class="table">


        </table>

    </div>

<?php
$canCheckin = Factory::getApplication()->getIdentity()->authorise(
        'core.manage',
        'com_pim.' . $this->item->id
    ) || $this->item->checked_out == Factory::getApplication()->getIdentity()->id; ?>
<?php
if ($canEdit && $this->item->checked_out == 0): ?>

    <a class="btn btn-outline-primary" href="<?php
    echo Route::_('index.php?option=com_pim&task=item.edit&id=' . $this->item->id); ?>"><?php
        echo Text::_("COM_PIM_EDIT_ITEM"); ?></a>
<?php
elseif ($canCheckin && $this->item->checked_out > 0) : ?>
    <a class="btn btn-outline-primary" href="<?php
    echo Route::_(
        'index.php?option=com_pim&task=item.checkin&id=' . $this->item->id . '&' . Session::getFormToken() . '=1'
    ); ?>"><?php
        echo Text::_("JLIB_HTML_CHECKIN"); ?></a>

<?php
endif; ?>

<?php
if (Factory::getApplication()->getIdentity()->authorise('core.delete', 'com_pim.item.' . $this->item->id)) : ?>

    <a class="btn btn-danger" rel="noopener noreferrer" href="#deleteModal" role="button" data-bs-toggle="modal">
        <?php
        echo Text::_("COM_PIM_DELETE_ITEM"); ?>
    </a>

    <?php
    echo HTMLHelper::_(
        'bootstrap.renderModal',
        'deleteModal',
        array(
            'title' => Text::_('COM_PIM_DELETE_ITEM'),
            'height' => '50%',
            'width' => '20%',

            'modalWidth' => '50',
            'bodyHeight' => '100',
            'footer' => '<button class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button><a href="' . Route::_(
                    'index.php?option=com_pim&task=item.remove&id=' . $this->item->id,
                    false,
                    2
                ) . '" class="btn btn-danger">' . Text::_('COM_PIM_DELETE_ITEM') . '</a>'
        ),
        Text::sprintf('COM_PIM_DELETE_CONFIRM', $this->item->id)
    ); ?>

<?php
endif; ?>