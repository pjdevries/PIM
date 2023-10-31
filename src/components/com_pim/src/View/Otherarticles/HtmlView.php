<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Pim
 * @author     Pieter-Jan de Vries <pieter@obix.nl>
 * @copyright  2023 Pieter-Jan de Vries
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Pim\Component\Pim\Site\View\OtherArticles;

// No direct access
defined('_JEXEC') or die;

use Exception;
use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * View class for a list of Items.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
	protected $items;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');

		parent::display($tpl);
	}
}
