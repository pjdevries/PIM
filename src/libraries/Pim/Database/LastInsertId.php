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

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

class LastInsertId
{
    public static function set(string $tableName, mixed $id): void
    {
        $session = Factory::getApplication()->getSession();

        /** @var Registry $lastInsertIds */
        $lastInsertIds = $session->get('lastInsertIds', new Registry());
        $lastInsertIds->set($tableName, $id);
        $session->set('lastInsertIds', $lastInsertIds);
    }

    public static function get(string $tableName): mixed
    {
        $session = Factory::getApplication()->getSession();

        /** @var Registry $lastInsertIds */
        $lastInsertIds = $session->get('lastInsertIds', new Registry());

        return $lastInsertIds->get($tableName, null);
    }
}