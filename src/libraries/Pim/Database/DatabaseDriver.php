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
    public static function instance(object $connectionParams, bool $forceNew = false): DatabaseInterface
    {
        static $instances = [];

        // Return instance is it already exists.
        if (isset($instances[$connectionParams->connectionName]) && !$forceNew) {
            return $instances[$connectionParams->connectionName];
        }

        // Create driver instance for these connection parameters.
        $dbConfig = [
            'driver' => $connectionParams->dbtype,
            'host' => $connectionParams->dbhost,
            'user' => $connectionParams->dbuser,
            'password' => $connectionParams->dbpassword,
            'database' => $connectionParams->dbname,
            'prefix' => $connectionParams->dbprefix,
        ];

        $instances[$connectionParams->connectionName] = (new DatabaseFactory())->getDriver('mysqli', $dbConfig);

        return $instances[$connectionParams->connectionName];
    }
}