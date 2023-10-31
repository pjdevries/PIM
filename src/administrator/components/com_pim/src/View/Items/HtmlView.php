<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Pim
 * @author     Pieter-Jan de Vries <pieter@obix.nl>
 * @copyright  2023 Pieter-Jan de Vries
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Pim\Component\Pim\Administrator\View\Items;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Pim\Component\Pim\Administrator\Helper\PimHelper;

/**
 * View class for a list of Items.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected $items;

    protected $pagination;

    protected $state;

    /**
     * Display the view
     *
     * @param string $tpl Template name
     *
     * @return void
     *
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        $this->sidebar = Sidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function addToolbar()
    {
        $state = $this->get('State');
        $canDo = PimHelper::getActions();

        ToolbarHelper::title(Text::_('COM_PIM_TITLE_ITEMS'), "jcc fab fa-meetup");

        $toolbar = Toolbar::getInstance('toolbar');

        // Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/src/View/Items';

        if (file_exists($formPath)) {
            if ($canDo->get('core.create')) {
                $toolbar->addNew('item.add');
            }
        }

        if ($canDo->get('core.edit.state')) {
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('fas fa-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();

            if (isset($this->items[0]->state)) {
                $childBar->publish('items.publish')->listCheck(true);
                $childBar->unpublish('items.unpublish')->listCheck(true);
                $childBar->archive('items.archive')->listCheck(true);
            } elseif (isset($this->items[0])) {
                // If this component does not use state then show a direct delete button as we can not trash
                $toolbar->delete('items.delete')
                    ->text('JTOOLBAR_EMPTY_TRASH')
                    ->message('JGLOBAL_CONFIRM_DELETE')
                    ->listCheck(true);
            }

            $childBar->standardButton('duplicate')
                ->text('JTOOLBAR_DUPLICATE')
                ->icon('fas fa-copy')
                ->task('items.duplicate')
                ->listCheck(true);

            if (isset($this->items[0]->checked_out)) {
                $childBar->checkin('items.checkin')->listCheck(true);
            }

            if (isset($this->items[0]->state)) {
                $childBar->trash('items.trash')->listCheck(true);
            }
        }


        // Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
            if ($this->state->get('filter.state') == ContentComponent::CONDITION_TRASHED && $canDo->get(
                    'core.delete'
                )) {
                $toolbar->delete('items.delete')
                    ->text('JTOOLBAR_EMPTY_TRASH')
                    ->message('JGLOBAL_CONFIRM_DELETE')
                    ->listCheck(true);
            }
        }

        if ($canDo->get('core.admin')) {
            $toolbar->preferences('com_pim');
        }

        // Set sidebar action
        Sidebar::setAction('index.php?option=com_pim&view=items');
    }

    /**
     * Method to order fields
     *
     * @return void
     */
    protected function getSortFields()
    {
        return array(
            'a.`id`' => Text::_('JGRID_HEADING_ID'),
            'a.`state`' => Text::_('JSTATUS'),
            'a.`ordering`' => Text::_('JGRID_HEADING_ORDERING'),
            'a.`title`' => Text::_('COM_PIM_ITEMS_TITLE'),
        );
    }

    /**
     * Check if state is set
     *
     * @param mixed $state State
     *
     * @return bool
     */
    public function getState($state)
    {
        return isset($this->state->{$state}) ? $this->state->{$state} : false;
    }
}
