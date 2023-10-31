<?php
/**
 * @package     PIM
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Pim\Plugin\WebServices\Pim\Extension;

defined('_JEXEC') || die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\ApiRouter;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

class Pim extends CMSPlugin implements SubscriberInterface
{
    protected $allowLegacyListeners = false;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onBeforeApiRoute' => 'registerRoutes',
        ];
    }

    /**
     * Register the Joomla API application routes for Akeeba Backup
     *
     * @param Event $event
     *
     * @return  void
     * @since   9.6.0
     */
    public function registerRoutes(Event $event): void
    {
        /** @var ApiRouter $router */
        [$router] = array_values($event->getArguments());

        $router->createCRUDRoutes(
            'v1/pim/items',
            'items',
            ['component' => 'com_pim']
        );
    }
}