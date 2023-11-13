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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

class ProgressdemoController extends BaseController
{
    public function insertRows(): void
    {
        $rowsDone = $this->input->getInt('rowsDone', 0);
        $chunkSize = $this->input->getInt('chunkSize', 5);

        // Load the model
        $model = $this->getModel('Progressdemo');

        // Set the MIME type for JSON output
        Factory::getApplication()->getDocument()->setMimeEncoding('application/json');

        // Invoke the model method to insert rows in chunks and output progress
        $model->insertRows($rowsDone, $chunkSize);

        $this->app->close();
    }
}
