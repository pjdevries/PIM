<?php
/**
 * @package     PIM
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Pim\MVC\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Pim\Database\DatabaseDriver;

class ItemsModel extends ListModel
{
    /**
     * Instantiates a model with a deviating database driver, allowing
     * access to an alternate database.
     *
     * @param   array                                             $config
     * @param   \Joomla\CMS\MVC\Factory\MVCFactoryInterface|null  $factory
     *
     * @throws \Exception
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null)
    {
        $config['dbo'] = DatabaseDriver::instance('drimmelendb');

        parent::__construct($config, $factory);
    }

    public function getItemCount(): int
    {
        $db = DatabaseDriver::instance();
        $q  = $db->getQuery(true);
        $q
            ->select('count(*) AS count')
            ->from($db->qn('#__items'));
        $db->setQuery($q);

        $num = $db->loadResult();

        return $num;
    }
}