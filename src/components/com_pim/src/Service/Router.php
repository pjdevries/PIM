<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Pim
 * @author     Pieter-Jan de Vries <pieter@obix.nl>
 * @copyright  2023 Pieter-Jan de Vries
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Pim\Component\Pim\Site\Service;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Categories\CategoryInterface;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;

/**
 * Class PimRouter
 *
 */
class Router extends RouterView
{
    private $noIDs;
    /**
     * The category factory
     *
     * @var    CategoryFactoryInterface
     *
     * @since  1.0.0
     */
    private $categoryFactory;

    /**
     * The category cache
     *
     * @var    array
     *
     * @since  1.0.0
     */
    private $categoryCache = [];

    public function __construct(
        SiteApplication $app,
        AbstractMenu $menu,
        CategoryFactoryInterface $categoryFactory,
        DatabaseInterface $db
    ) {
        $params = Factory::getApplication()->getParams('com_pim');
        $this->noIDs = (bool)$params->get('sef_ids');
        $this->categoryFactory = $categoryFactory;


        $items = new RouterViewConfiguration('items');
        $this->registerView($items);
        $ccItem = new RouterViewConfiguration('item');
        $ccItem->setKey('id')->setParent($items);
        $this->registerView($ccItem);
        $itemform = new RouterViewConfiguration('itemform');
        $itemform->setKey('id');
        $this->registerView($itemform);

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));
    }


    /**
     * Method to get the segment(s) for an item
     *
     * @param string $id ID of the item to retrieve the segments for
     * @param array $query The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getItemSegment($id, $query)
    {
        return array((int)$id => $id);
    }

    /**
     * Method to get the segment(s) for an itemform
     *
     * @param string $id ID of the itemform to retrieve the segments for
     * @param array $query The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getItemformSegment($id, $query)
    {
        return $this->getItemSegment($id, $query);
    }


    /**
     * Method to get the segment(s) for an item
     *
     * @param string $segment Segment of the item to retrieve the ID for
     * @param array $query The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getItemId($segment, $query)
    {
        return (int)$segment;
    }

    /**
     * Method to get the segment(s) for an itemform
     *
     * @param string $segment Segment of the itemform to retrieve the ID for
     * @param array $query The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getItemformId($segment, $query)
    {
        return $this->getItemId($segment, $query);
    }

    /**
     * Method to get categories from cache
     *
     * @param array $options The options for retrieving categories
     *
     * @return  CategoryInterface  The object containing categories
     *
     * @since   1.0.0
     */
    private function getCategories(array $options = []): CategoryInterface
    {
        $key = serialize($options);

        if (!isset($this->categoryCache[$key])) {
            $this->categoryCache[$key] = $this->categoryFactory->createCategory($options);
        }

        return $this->categoryCache[$key];
    }
}
