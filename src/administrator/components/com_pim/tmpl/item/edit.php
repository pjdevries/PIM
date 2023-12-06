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

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');
?>

<form
        action="<?php
        echo Route::_('index.php?option=com_pim&layout=edit&id=' . (int)$this->item->id); ?>"
        method="post" enctype="multipart/form-data" name="adminForm" id="item-form"
        class="form-validate form-horizontal">


    <?php
    echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'item')); ?>
    <?php
    echo HTMLHelper::_('uitab.addTab', 'myTab', 'item', Text::_('COM_PIM_TAB_ITEM', true)); ?>
    <div class="row-fluid">
        <div class="col-md-12 form-horizontal">
            <fieldset class="adminform">
                <legend><?php
                    echo Text::_('COM_PIM_FIELDSET_ITEM'); ?></legend>
                <?php
                echo $this->form->renderField('title'); ?>
                <?php
                echo $this->form->renderField('alias'); ?>
                <?php
                echo $this->form->renderField('files'); ?>
                <?php
                if ($this->state->params->get('save_history', 1)) : ?>
                    <div class="control-group">
                        <div class="control-label"><?php
                            echo $this->form->getLabel('version_note'); ?></div>
                        <div class="controls"><?php
                            echo $this->form->getInput('version_note'); ?></div>
                    </div>
                <?php
                endif; ?>
            </fieldset>
        </div>
    </div>
    <?php
    echo HTMLHelper::_('uitab.endTab'); ?>
    <input type="hidden" name="jform[id]" value="<?php
    echo $this->item->id; ?>"/>
    <input type="hidden" name="jform[state]" value="<?php
    echo $this->item->state; ?>"/>
    <input type="hidden" name="jform[ordering]" value="<?php
    echo $this->item->ordering; ?>"/>
    <input type="hidden" name="jform[checked_out]" value="<?php
    echo $this->item->checked_out; ?>"/>
    <input type="hidden" name="jform[checked_out_time]" value="<?php
    echo $this->item->checked_out_time; ?>"/>
    <?php
    echo $this->form->renderField('created_by'); ?>
    <?php
    echo $this->form->renderField('modified_by'); ?>

    <?php
    if (Factory::getApplication()->getIdentity()->authorise('core.admin', 'pim')) : ?>
        <?php
        echo HTMLHelper::_(
            'uitab.addTab',
            'myTab',
            'permissions',
            Text::_('JGLOBAL_ACTION_PERMISSIONS_LABEL', true)
        ); ?>
        <?php
        echo $this->form->getInput('rules'); ?>
        <?php
        echo HTMLHelper::_('uitab.endTab'); ?>
    <?php
    endif; ?>
    <?php
    echo HTMLHelper::_('uitab.endTabSet'); ?>

    <input type="hidden" name="task" value=""/>
    <?php
    echo HTMLHelper::_('form.token'); ?>

</form>
