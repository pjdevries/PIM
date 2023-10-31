<?php
/**
 * @package     PIM
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Pim\Component\Pim\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Pim\Component\Pim\Site\Model\OtherarticlesModel;
use Pim\Database\DatabaseDriver;
use Pim\Database\DatabaseParamsHelper;

class OtherarticlesController extends FormController
{
    /**
     * Proxy for getModel.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional
     * @param   array   $config  Configuration array for model. Optional
     *
     * @return  object	The model
     *
     * @since   1.0.0
     */
    public function getModel($name = 'Otherarticles', $prefix = 'Site', $config =[])
    {
        $helper = new DatabaseParamsHelper('system', 'pim');

        /** @var OtherarticlesModel $model */
        $model = parent::getModel($name, $prefix, $config);
        $model->setDatabase(DatabaseDriver::instance($helper->getConnectionParams('otherarticles')));

        return $model;
    }
}