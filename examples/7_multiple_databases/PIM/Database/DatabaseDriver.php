<?php
/**
 * @package     PIM
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace PIM\Database;

\defined('_JEXEC') or die;

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseFactory;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;

class DatabaseDriver
{
    /**
     * Singleton database driver instantion.
     *
     * @return \Joomla\Database\DatabaseInterface
     *
     * @since version 1.0.0
     */
    public static function instance(): DatabaseInterface
    {
        static $instance;

        // Only create if it doesn't yet exist.
        if (!$instance) {
            // Get database details from deschrijn system plugin parameters.
            $plugin = PluginHelper::getPlugin('system', 'alternatedatabase');

            // Get plugin params
            $pluginParams = new Registry($plugin->params);
            $dbConfig = [
                'driver' => $pluginParams->get('dbtype'),
                'host' => $pluginParams->get('dbhost'),
                'user' => $pluginParams->get('dbuser'),
                'password' => $pluginParams->get('dbpassword'),
                'database' => $pluginParams->get('dbname'),
            ];

            $instance = (new DatabaseFactory())->getDriver('mysqli', $dbConfig);

            // Set date/time language default to Dutch.
            $instance->setQuery("SET lc_time_names = 'nl_NL'");
            $instance->execute();
        }

        return $instance;
    }
}