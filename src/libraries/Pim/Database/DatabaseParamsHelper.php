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

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

class DatabaseParamsHelper
{
    private object $pluginData;

    /**
     * @param object $pluginData
     */
    public function __construct(string $pluginType, string $pluginName)
    {
        $this->pluginData = PluginHelper::getPlugin($pluginType, $pluginName);
    }

    public function getConnectionParams(string $connectionName): ?object
    {
        // Get plugin params.
        $pluginParams = new Registry($this->pluginData->params);

        // Get database params.
        $databaseParams = $pluginParams->get('database');

        // Get database params for requested connection.
        $connectionParams = array_filter(
            array_values((array)$databaseParams),
            fn(object $p) => $p->connectionName === $connectionName
        );

        return $connectionParams[0] ?? null;
    }
}