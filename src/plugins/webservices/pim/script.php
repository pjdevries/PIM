<?php
/**
 * @package    PIM System Plugin
 *
 * @author     Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright  Copyright © 2023 Obix webtechniek. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://www.obix.nl
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Adapter\PluginAdapter;

/**
 * Pim script file.
 *
 * @package   PIM System Plugin
 * @since     1.0.0
 */
class plgWebservicesPimInstallerScript
{
    public function __construct()
    {
        $this->minimumJoomla = '4.3';
        $this->minimumPhp = '8.2';
    }

    /**
     * Called after any type of action.
     *
     * @param string $action Which action is happening (install|uninstall|discover_install|update)
     * @param PluginAdapter $adapter The object responsible for running this script
     *
     * @return  void
     */
    public function postflight($action, PluginAdapter $adapter): void
    {
        // Enable plugin on first installation only.
        if ($action === 'install' || $action === 'discover_install') {
            $this->publish();
        }
    }

    public function publish(): void
    {
        // Enable plugin on first installation only.
        $db = Factory::getDbo();
        $query = sprintf(
            'UPDATE %s SET %s = 1 WHERE %s = %s AND %s = %s',
            $db->quoteName('#__extensions'),
            $db->quoteName('enabled'),
            $db->quoteName('type'),
            $db->quote('plugin'),
            $db->quoteName('name'),
            $db->quote('PLG_WEBSERVICES_PIM')
        );
        $db->setQuery($query);
        $db->execute();
    }
}
