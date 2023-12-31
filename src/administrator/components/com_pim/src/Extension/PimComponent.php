<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Pim
 * @author     Pieter-Jan de Vries <pieter@obix.nl>
 * @copyright  2023 Pieter-Jan de Vries
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Pim\Component\Pim\Administrator\Extension;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Association\AssociationServiceTrait;
use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Joomla\CMS\Tag\TagServiceTrait;
use Pim\Component\Pim\Administrator\Service\Html\PIM;
use Psr\Container\ContainerInterface;

/**
 * Component class for Pim
 *
 * @since  1.0.0
 */
class PimComponent extends MVCComponent implements RouterServiceInterface, BootableExtensionInterface,
                                                   CategoryServiceInterface
{
    use AssociationServiceTrait;
    use RouterServiceTrait;
    use HTMLRegistryAwareTrait;
    use CategoryServiceTrait, TagServiceTrait {
        CategoryServiceTrait::getTableNameForSection insteadof TagServiceTrait;
        CategoryServiceTrait::getStateColumnForSection insteadof TagServiceTrait;
    }

    /** @inheritdoc */
    public function boot(ContainerInterface $container)
    {
        $db = $container->get('DatabaseDriver');
        $this->getRegistry()->register('pim', new PIM($db));
    }


    /**
     * Returns the table for the count items functions for the given section.
     *
     * @param string    The section
     *
     * * @return  string|null
     *
     * @since   4.0.0
     */
    protected function getTableNameForSection(string $section = null)
    {
    }

    /**
     * Adds Count Items for Category Manager.
     *
     * @param \stdClass[] $items The category objects
     * @param string $section The section
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function countItems(array $items, string $section)
    {
    }
}