<?php
/**
 * @package     WebwinkelKeur
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

defined('_JEXEC') || die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use PIM\Plugin\System\Pim\Extension\Pim;

return new class implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $config = (array)PluginHelper::getPlugin('system', 'wwk');
                $subject = $container->get(DispatcherInterface::class);

                /** @var \Joomla\CMS\Plugin\CMSPlugin $plugin */
                /** @var \Joomla\CMS\Plugin\CMSPlugin $plugin */
                $plugin = new Pim($subject, $config);
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};