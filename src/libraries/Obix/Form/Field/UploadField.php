<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Obix\Form\Field;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormField;
use SimpleXMLElement;

defined('JPATH_PLATFORM') or die;

class UploadField extends FormField
{
    /**
     * The form field type.
     *
     * @var    string
     */
    protected $type = 'Obixupload';

    /**
     * Name of the layout being used to render the field
     *
     * @var    string
     */
    protected $layout = 'obix.form.field.upload';

    /**
     * Method to get certain otherwise inaccessible properties from the form field object.
     *
     * @param string $name The property name for which to get the value.
     *
     * @return  mixed  The property value or null.
     */
    public function __get($name)
    {
        switch ($name) {
            default:
                return parent::__get($name);
        }
    }

    /**
     * Method to set certain otherwise inaccessible properties of the form field object.
     *
     * @param string $name The property name for which to set the value.
     * @param mixed $value The value of the property.
     *
     * @return  void
     */
    public function __set($name, $value)
    {
        switch ($name) {
            default:
                parent::__set($name, $value);
        }
    }

    /**
     * Method to attach a JForm object to the field.
     *
     * @param SimpleXMLElement $element The SimpleXMLElement object representing the `<field>` tag for the form field object.
     * @param mixed $value The form field value to validate.
     * @param string $group The field name group control value. This acts as an array container for the field.
     *                                      For example if the field has name="foo" and the group value is set to "bar" then the
     *                                      full field name would end up being "bar[foo]".
     *
     * @return  boolean  True on success.
     *
     * @see     FormField::setup()
     */
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        return parent::setup($element, $value, $group);
    }

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        return $this->getRenderer($this->layout)->render($this->getLayoutData());
    }

    /**
     * Method to get the data to be passed to the layout for rendering.
     *
     * @return  array
     */
    protected function getLayoutData()
    {
        $data = parent::getLayoutData();

        $extraData = array(
            'value' => $this->value,
        );

        return array_merge($data, $extraData);
    }

    public static function getFieldAttributes(Form $form, string $fieldName): array
    {
    }
}
