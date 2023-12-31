<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Pim
 * @author     Pieter-Jan de Vries <pieter@obix.nl>
 * @copyright  2023 Pieter-Jan de Vries
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Pim\Component\Pim\Site\Field;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

/**
 * Class SubmitField
 *
 * @since  1.0.0
 */
class SubmitField extends FormField
{
    protected $type = 'submit';

    protected $value;

    protected $for;

    /**
     * Get a form field markup for the input
     *
     * @return string
     */
    public function getInput()
    {
        $this->value = $this->getAttribute('value');

        return '<button id="' . $this->id . '"'
            . ' name="submit_' . $this->for . '"'
            . ' value="' . $this->value . '"'
            . ' title="' . Text::_('JSEARCH_FILTER_SUBMIT') . '"'
            . ' class="btn" style="margin-top: -10px;">'
            . Text::_('JSEARCH_FILTER_SUBMIT')
            . ' </button>';
    }
}
