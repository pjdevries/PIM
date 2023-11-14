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

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Items class.
 *
 * @since  1.0.0
 */
class AjaxitemsController extends FormController
{
    /**
     * Proxy for getModel.
     *
     * @param string $name The model name. Optional.
     * @param string $prefix The class prefix. Optional
     * @param array $config Configuration array for model. Optional
     *
     * @return  object    The model
     *
     * @since   1.0.0
     */
    public function getModel($name = 'Items', $prefix = 'Site', $config = array())
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }
}
