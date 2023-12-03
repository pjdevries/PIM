<?php
/**
 * @package     ObixUploads
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Adapter\PluginAdapter;

class PlgFieldsObixUploadInstallerScript
{
    // The extension will only install on PHP 8.0.0 or later
    protected $minimumPhp = '8.2';

    // The extension will only install on Joomla 4.2.0 or later
    protected $minimumJoomla = '4.3';

	/**
	 * Called after any type of action.
	 *
	 * @param   string         $action   Which action is happening (install|uninstall|discover_install|update)
	 * @param   PluginAdapter  $adapter  The object responsible for running this script
	 *
	 * @return  void
	 */
	public function postflight($action, $adapter): void
	{
		// Enable plugin on first installation only.
		if ($action === 'install' || $action === 'discover_install')
		{
			$this->publish();
		}
	}

	private function publish()
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = sprintf(
			'UPDATE %s SET %s = 1 WHERE %s = %s AND %s = %s',
			$db->quoteName('#__extensions'),
			$db->quoteName('enabled'),
			$db->quoteName('type'), $db->quote('plugin'),
			$db->quoteName('name'), $db->quote('PLG_FIELDS_OBIXUPLOAD')
		);
		$db->setQuery($query);
		$db->execute();
	}
}
