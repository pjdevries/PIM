<?php
/**
 * @package    PIM System Plugin
 *
 * @author     Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright  Copyright © 2023 Obix webtechniek. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://www.obix.nl
 */

namespace Pim\Plugin\System\Pim\Extension;

defined('_JEXEC') or die;

use JLoader;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Pim\Database\LastInsertId;

/**
 * Pim plugin.
 *
 * @package   PIM System Plugin
 * @since     1.0.0
 */
class Pim extends CMSPlugin implements SubscriberInterface
{
    /**
     * Application object
     *
     * @var    CMSApplication
     * @since  1.0.0
     */
    protected $app;

    /**
     * Database object
     *
     * @var    DatabaseDriver
     * @since  1.0.0
     */
    protected $db;

    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var    boolean
     * @since  1.0.0
     */
    protected $autoloadLanguage = true;

    public function __construct(&$subject, $config = [])
    {
        parent::__construct($subject, $config);

        JLoader::registerNamespace('Pim', JPATH_LIBRARIES . '/Pim');
        JLoader::registerNamespace('Obix', JPATH_LIBRARIES . '/Obix');
    }

    public static function getSubscribedEvents(): array
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            'onAfterRoute' => 'handleAfterRoute',
            'onTableAfterStore' => 'handleTableAfterStore'
        ];
    }

    public function handleAfterRoute(Event $event): void
    {
        $lang = $this->app->getLanguage();
        $langTag = $lang->getTag();

        if ($this->app->isClient('administrator')) {
            // administrator/language/en-GB/com_config.ini
            $basePath = JPATH_ADMINISTRATOR;
            $lang->load('com_config', $basePath, $langTag, true);
        }

        $basePath = JPATH_LIBRARIES . '/DeSchrijn';
        $lang->load('lib_deschrijn', $basePath, $langTag, true);

    }

    public function handleTableAfterStore(Event $event): void
    {
        /** @var $table Table */
        /** @var $result bool */
        [$table, &$result] = array_values($event->getArguments());

        LastInsertId::set($table->getTableName(), $table->getId());
    }
}
