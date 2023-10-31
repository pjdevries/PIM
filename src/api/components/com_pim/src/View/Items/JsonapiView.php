<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Pim
 * @author     Pieter-Jan de Vries <pieter@obix.nl>
 * @copyright  2023 Pieter-Jan de Vries
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Pim\Component\Pim\Api\View\Items;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\JsonApiView as BaseApiView;

/**
 * The Items view
 *
 * @since  1.0.0
 */
class JsonApiView extends BaseApiView
{
    /**
     * The fields to render item in the documents
     *
     * @var    array
     * @since  1.0.0
     */
    protected $fieldsToRenderItem = [
        'title'
    ];

    /**
     * The fields to render items in the documents
     *
     * @var    array
     * @since  1.0.0
     */
    protected $fieldsToRenderList = [
        'title'
    ];

    public function displayList(array $items = null)
    {
        parent::displayList($items);
    }
}