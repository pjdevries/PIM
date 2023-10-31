<?php
/**
 * @package     PIM
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Pim\Database;

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
    public static function instance(string $name): DatabaseInterface
    {
        static $instance;

        // Only create if it doesn't yet exist.
        if (!$instance) {
            // Get database details from deschrijn system plugin parameters.
            $plugin = PluginHelper::getPlugin('system', 'pim');
            // Get plugin params.
            $pluginParams = new Registry($plugin->params);
            // Get database params.
            $databaseParams = $pluginParams->get('database');
            // Get database params for requested connection.
            $connectionParams = array_filter(
                array_values((array) $databaseParams),
                fn(object $p) => $p->connectionName === $name
            );

            $dbConfig = [
                'driver' => $connectionParams[0]->dbtype,
                'host' => $connectionParams[0]->dbhost,
                'user' => $connectionParams[0]->dbuser,
                'password' => $connectionParams[0]->dbpassword,
                'database' => $connectionParams[0]->dbname,
            ];

            $instance = (new DatabaseFactory())->getDriver('mysqli', $dbConfig);

            // Set date/time language default to Dutch.
            $instance->setQuery("SET lc_time_names = 'nl_NL'");
            $instance->execute();
        }

        return $instance;
    }
}