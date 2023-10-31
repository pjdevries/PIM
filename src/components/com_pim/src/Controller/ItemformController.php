<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Pim
 * @author     Pieter-Jan de Vries <pieter@obix.nl>
 * @copyright  2023 Pieter-Jan de Vries
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Pim\Component\Pim\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;

/**
 * Item class.
 *
 * @since  1.0.0
 */
class ItemformController extends FormController
{
    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   1.0.0
     *
     */
    public function edit($key = null, $urlVar = null)
    {
        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int)$this->app->getUserState('com_pim.edit.item.id');
        $editId = $this->input->getInt('id', 0);

        // Set the user id for the user to edit in the session.
        $this->app->setUserState('com_pim.edit.item.id', $editId);

        // Get the model.
        $model = $this->getModel('Itemform', 'Site');

        // Check out the item
        if ($editId) {
            $model->checkout($editId);
        }

        // Check in the previous user.
        if ($previousId) {
            $model->checkin($previousId);
        }

        // Redirect to the edit screen.
        $this->setRedirect(Route::_('index.php?option=com_pim&view=itemform&layout=edit', false));
    }

    /**
     * Method to save data.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   1.0.0
     */
    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        $this->checkToken();

        // Initialise variables.
        $model = $this->getModel('Itemform', 'Site');

        // Get the user data.
        $data = $this->input->get('jform', array(), 'array');

        // Validate the posted data.
        $form = $model->getForm();

        if (!$form) {
            throw new \Exception($model->getError(), 500);
        }

        // Send an object which can be modified through the plugin event
        $objData = (object)$data;
        $this->app->triggerEvent(
            'onContentNormaliseRequestData',
            array($this->option . '.' . $this->context, $objData, $form)
        );
        $data = (array)$objData;

        // Validate the posted data.
        $data = $model->validate($form, $data);

        // Check for errors.
        if ($data === false) {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
                if ($errors[$i] instanceof \Exception) {
                    $this->app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                } else {
                    $this->app->enqueueMessage($errors[$i], 'warning');
                }
            }

            $jform = $this->input->get('jform', array(), 'ARRAY');

            // Save the data in the session.
            $this->app->setUserState('com_pim.edit.item.data', $jform);

            // Redirect back to the edit screen.
            $id = (int)$this->app->getUserState('com_pim.edit.item.id');
            $this->setRedirect(Route::_('index.php?option=com_pim&view=itemform&layout=edit&id=' . $id, false));

            $this->redirect();
        }

        // Attempt to save the data.
        $return = $model->save($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $this->app->setUserState('com_pim.edit.item.data', $data);

            // Redirect back to the edit screen.
            $id = (int)$this->app->getUserState('com_pim.edit.item.id');
            $this->setMessage(Text::sprintf('Save failed', $model->getError()), 'warning');
            $this->setRedirect(Route::_('index.php?option=com_pim&view=itemform&layout=edit&id=' . $id, false));
            $this->redirect();
        }

        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        // Clear the profile id from the session.
        $this->app->setUserState('com_pim.edit.item.id', null);

        // Redirect to the list screen.
        if (!empty($return)) {
            $this->setMessage(Text::_('COM_PIM_ITEM_SAVED_SUCCESSFULLY'));
        }

        $menu = Factory::getApplication()->getMenu();
        $item = $menu->getActive();
        $url = (empty($item->link) ? 'index.php?option=com_pim&view=items' : $item->link);
        $this->setRedirect(Route::_($url, false));

        // Flush the data from the session.
        $this->app->setUserState('com_pim.edit.item.data', null);

        // Invoke the postSave method to allow for the child class to access the model.
        $this->postSaveHook($model, $data);
    }

    /**
     * Method to abort current operation
     *
     * @return void
     *
     * @throws Exception
     */
    public function cancel($key = null)
    {
        // Get the current edit id.
        $editId = (int)$this->app->getUserState('com_pim.edit.item.id');

        // Get the model.
        $model = $this->getModel('Itemform', 'Site');

        // Check in the item
        if ($editId) {
            $model->checkin($editId);
        }

        $menu = Factory::getApplication()->getMenu();
        $item = $menu->getActive();
        $url = (empty($item->link) ? 'index.php?option=com_pim&view=items' : $item->link);
        $this->setRedirect(Route::_($url, false));
    }

    /**
     * Method to remove data
     *
     * @return  void
     *
     * @throws  Exception
     *
     * @since   1.0.0
     */
    public function remove()
    {
        $model = $this->getModel('Itemform', 'Site');
        $pk = $this->input->getInt('id');

        // Attempt to save the data
        try {
            // Check in before delete
            $return = $model->checkin($return);
            // Clear id from the session.
            $this->app->setUserState('com_pim.edit.item.id', null);

            $menu = $this->app->getMenu();
            $item = $menu->getActive();
            $url = (empty($item->link) ? 'index.php?option=com_pim&view=items' : $item->link);

            if ($return) {
                $model->delete($pk);
                $this->setMessage(Text::_('COM_PIM_ITEM_DELETED_SUCCESSFULLY'));
            } else {
                $this->setMessage(Text::_('COM_PIM_ITEM_DELETED_UNSUCCESSFULLY'), 'warning');
            }


            $this->setRedirect(Route::_($url, false));
            // Flush the data from the session.
            $this->app->setUserState('com_pim.edit.item.data', null);
        } catch (\Exception $e) {
            $errorType = ($e->getCode() == '404') ? 'error' : 'warning';
            $this->setMessage($e->getMessage(), $errorType);
            $this->setRedirect('index.php?option=com_pim&view=items');
        }
    }

    /**
     * Function that allows child controller access to model data
     * after the data has been saved.
     *
     * @param BaseDatabaseModel $model The data model object.
     * @param array $validData The validated data.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function postSaveHook(BaseDatabaseModel $model, $validData = array())
    {
    }
}
