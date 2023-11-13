<?php
/**
 * @package     PIM
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Pim\Component\Pim\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class ProgressdemoModel extends BaseDatabaseModel
{
    private $totalRows = 50;
    private $sleepTime = 100000;    // in microseconds

    public function insertRows($rowsDone, $chunkSize): void
    {
        $rowsRemaining = $this->totalRows - $rowsDone;

        if ($rowsRemaining > 0) {
            // Simulate inserting a chunk of rows into the database
            usleep($this->sleepTime);

            $rowsDone += $chunkSize;
        }

        // Calculate progress
        $progress = round(($rowsDone / $this->totalRows) * 100);

        // Output progress as JSON
        echo json_encode([
            'rowsDone' => $rowsDone,
            'totalRows' => $this->totalRows,
            'progress' => $progress
        ]);

        // Flush the output to the browser
        ob_flush();
        flush();
    }
}