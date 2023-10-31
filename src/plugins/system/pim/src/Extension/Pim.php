<?php
/**
 * @package    PIM System Plugin
 *
 * @author     Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright  Copyright © 2023 Obix webtechniek. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://www.obix.nl
 */

namespace PIM\Plugin\System\Pim\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

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
    }

    public static function getSubscribedEvents(): array
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            'onAfterRoute' => 'handleAfterRoute'
        ];
    }

    public function handleAfterRoute(Event $event)
    {
        $lang = $this->app->getLanguage();
        $langTag = $lang->getTag();

        if ($this->app->isClient('administrator')) {
            // administrator/language/en-GB/com_config.ini
            $basePath = JPATH_ADMINISTRATOR;
            $lang->load('com_config', $basePath, $langTag, true);
        }
    }

}
