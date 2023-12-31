<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Pim
 * @author     Pieter-Jan de Vries <pieter@obix.nl>
 * @copyright  2023 Pieter-Jan de Vries
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Pim\Component\Pim\Site\Model;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use Obix\Filesystem\Upload\Handler;
use Obix\Filesystem\Upload\Prerequisites;

/**
 * Pim model.
 *
 * @since  1.0.0
 */
class ItemformModel extends FormModel
{
    private $item = null;


    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return  void
     *
     * @throws  \Exception
     * @since   1.0.0
     *
     */
    protected function populateState()
    {
        $app = Factory::getApplication('com_pim');

        // Load state from the request userState on edit or from the passed variable on default
        if (Factory::getApplication()->input->get('layout') == 'edit') {
            $id = Factory::getApplication()->getUserState('com_pim.edit.item.id');
        } else {
            $id = Factory::getApplication()->input->get('id');
            Factory::getApplication()->setUserState('com_pim.edit.item.id', $id);
        }

        $this->setState('item.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();

        if (isset($params_array['item_id'])) {
            $this->setState('item.id', $params_array['item_id']);
        }

        $this->setState('params', $params);
    }

    /**
     * Method to get an ojbect.
     *
     * @param integer $id The id of the object to get.
     *
     * @return  Object|boolean Object on success, false on failure.
     *
     * @throws  \Exception
     */
    public function getItem($id = null)
    {
        if ($this->item === null) {
            $this->item = false;

            if (empty($id)) {
                $id = $this->getState('item.id');
            }

            // Get a level row instance.
            $table = $this->getTable();
            $properties = $table->getProperties();
            $this->item = ArrayHelper::toObject($properties, CMSObject::class);

            if ($table !== false && $table->load($id) && !empty($table->id)) {
                $user = Factory::getApplication()->getIdentity();
                $id = $table->id;


                if ($id) {
                    $canEdit = $user->authorise('core.edit', 'com_pim.item.' . $id) || $user->authorise(
                            'core.create',
                            'com_pim.item.' . $id
                        );
                } else {
                    $canEdit = $user->authorise('core.edit', 'com_pim') || $user->authorise('core.create', 'com_pim');
                }

                if (!$canEdit && $user->authorise('core.edit.own', 'com_pim.item.' . $id)) {
                    $canEdit = $user->id == $table->created_by;
                }

                if (!$canEdit) {
                    throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
                }

                // Check published state.
                if ($published = $this->getState('filter.published')) {
                    if (isset($table->state) && $table->state != $published) {
                        return $this->item;
                    }
                }

                // Convert the Table to a clean CMSObject.
                $properties = $table->getProperties(1);
                $this->item = ArrayHelper::toObject($properties, CMSObject::class);
            }
        }

        return $this->item;
    }

    /**
     * Method to get the table
     *
     * @param string $type Name of the Table class
     * @param string $prefix Optional prefix for the table class name
     * @param array $config Optional configuration array for Table object
     *
     * @return  Table|boolean Table if found, boolean false on failure
     */
    public function getTable($type = 'Item', $prefix = 'Administrator', $config = array())
    {
        return parent::getTable($type, $prefix, $config);
    }

    /**
     * Get an item by alias
     *
     * @param string $alias Alias string
     *
     * @return int Element id
     */
    public function getItemIdByAlias($alias)
    {
        $table = $this->getTable();
        $properties = $table->getProperties();

        if (!in_array('alias', $properties)) {
            return null;
        }

        $table->load(array('alias' => $alias));
        $id = $table->id;


        return $id;
    }

    /**
     * Method to check in an item.
     *
     * @param integer $id The id of the row to check out.
     *
     * @return  boolean True on success, false on failure.
     *
     * @since   1.0.0
     */
    public function checkin($id = null)
    {
        // Get the id.
        $id = (!empty($id)) ? $id : (int)$this->getState('item.id');

        if ($id) {
            // Initialise the table
            $table = $this->getTable();

            // Attempt to check the row in.
            if (method_exists($table, 'checkin')) {
                if (!$table->checkin($id)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to check out an item for editing.
     *
     * @param integer $id The id of the row to check out.
     *
     * @return  boolean True on success, false on failure.
     *
     * @since   1.0.0
     */
    public function checkout($id = null)
    {
        // Get the user id.
        $id = (!empty($id)) ? $id : (int)$this->getState('item.id');

        if ($id) {
            // Initialise the table
            $table = $this->getTable();

            // Get the current user object.
            $user = Factory::getApplication()->getIdentity();

            // Attempt to check the row out.
            if (method_exists($table, 'checkout')) {
                if (!$table->checkout($user->get('id'), $id)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML
     *
     * @param array $data An optional array of data for the form to interogate.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  Form|false    A Form object on success, false on failure
     *
     * @since   1.0.0
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_pim.item', 'itemform', array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  array  The default data is an empty array.
     * @since   1.0.0
     */
    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_pim.edit.item.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        if ($data) {
            return $data;
        }

        return array();
    }

    /**
     * Method to save the form data.
     *
     * @param array $data The form data
     *
     * @return  bool
     *
     * @throws  \Exception
     * @since   1.0.0
     */
    public function save($data)
    {
        $app = Factory::getApplication();
        $context = $this->option . '.' . $this->name;

        $id = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('item.id');
        $state = (!empty($data['state'])) ? 1 : 0;
        $user = Factory::getApplication()->getIdentity();
        $isNew = true;

        if ($id) {
            // Check the user can edit this item
            $authorised = $user->authorise('core.edit', 'com_pim.item.' . $id) || $authorised = $user->authorise(
                    'core.edit.own',
                    'com_pim.item.' . $id
                );
            $isNew = false;
        } else {
            // Check the user can create new items in this section
            $authorised = $user->authorise('core.create', 'com_pim');
        }

        if ($authorised !== true) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $table = $this->getTable();

        if (!empty($id)) {
            $table->load($id);
        }

        try {
            // Include the plugins for the save events.
            PluginHelper::importPlugin('content');

            // Wrap database and/or file system manipulations in a database transaction.
            $db = $table->getDbo();
            $db->transactionStart();

            $data['files'] = json_encode([]);

            // Get uploaded files from request.
            $allFiles = $app->getInput()->files->get('jform', [], 'RAW');

            if (count($allFiles)) {
                $handlers = Handler::handle($allFiles, $this->getForm([], false));

                foreach ($handlers as $fieldName => $handler) {
                    $uploadedFiles = $handler->getSuccesful();

                    if (!count($uploadedFiles)) {
                        continue;
                    }

                    $oldFilesData = json_decode($table->files ?: '{}', true);
                    $maxFileId = array_reduce(
                        $oldFilesData,
                        fn(int $id, array $fileData) => max($id, $fileData['id']),
                        0
                    );
                    $addionalFilesData = array_map(function (array $file) use (&$maxFileId) {
                        return [
                            'id' => ++$maxFileId,
                            'name' => $file['name'],
                            'full_path' => $file['full_path'] ?? '',
                            'dest_path' => $file['dest_path']
                        ];
                    }, $uploadedFiles);

                    $newFilesData = [
                        ...$oldFilesData,
                        ...$addionalFilesData
                    ];

                    $data[$fieldName] = json_encode($newFilesData);
                }
            }

            $result = $app->triggerEvent('onContentBeforeSave', [$context, $table, $isNew, $data]);

            if (\in_array(false, $result, true)) {
                throw new \RuntimeException($table->getError());
            }

            if ($table->save($data) !== true) {
                throw new \RuntimeException($table->getError());
            }

            $app->triggerEvent('onContentAfterSave', [$context, $table, $isNew, $data]);

            // If there were no critical database and/or file system manipulation errors, commit the transaction.
            $db->transactionCommit();

            return $table->id;
        } catch (\Exception $e) {
            // On exception, rollback the transaction.
            $db->transactionRollback();

            /** @var Handler $handler */
            foreach ($handlers as $handler) {
                $handler->remove();
            }

            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

            return false;
        }
    }

    public function getUploadPrerequisites(array $fieldNames): array
    {
        $form = $this->getForm([], false);
        $prerequisites = [];

        foreach ($fieldNames as $fieldName) {
            $fieldXml = $form->getFieldXml($fieldName);
            $dummy = new Prerequisites((string)$fieldXml['destdir'], (string)$fieldXml['maxuploadsize']);

            $field = $form->getField($fieldName);
            $prerequisites[$fieldName] = new Prerequisites(
                $field->getAttribute('destdir'),
                $field->getAttribute('maxuploadsize')
            );
        }

        return $prerequisites;
    }

    /**
     * Method to delete data
     *
     * @param array $id Item primary key
     *
     * @return  int  The id of the deleted item
     *
     * @throws  \Exception
     *
     * @since   1.0.0
     */
    public function delete(&$pks)
    {
        $user = Factory::getApplication()->getIdentity();

        $id = is_array($pks) ? $pks[0] : $pks;

        if (empty($id)) {
            $id = (int)$this->getState('item.id');
        }

        if ($id == 0 || $this->getItem($id) == null) {
            throw new \Exception(Text::_('COM_PIM_ITEM_DOESNT_EXIST'), 404);
        }

        if ($user->authorise('core.delete', 'com_pim.item.' . $id) !== true) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $table = $this->getTable();

        if ($table->delete($id) !== true) {
            throw new \Exception(Text::_('JERROR_FAILED'), 501);
        }

        return $id;
    }

    /**
     * Check if data can be saved
     *
     * @return bool
     */
    public function getCanSave()
    {
        $table = $this->getTable();

        return $table !== false;
    }

}
