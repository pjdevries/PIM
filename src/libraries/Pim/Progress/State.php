<?php
/**
 * @package     PIM
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Pim\Progress;

\defined('_JEXEC') or die;

class State
{
    private int $totalRows = 50;
    private int $rowsDone = 0;

    private int $progressPercentage = 0;

    /**
     * @param int $totalRows
     */
    public function __construct(int $totalRows)
    {
        $this->totalRows = $totalRows;
    }

    public function isDone(): bool
    {
        return $this->rowsDone >= $this->totalRows;
    }

    /**
     * @param int $rowsDone
     * @return State
     */
    public function addRowsDone(int $rowsDone): State
    {
        $this->rowsDone += $rowsDone;

        return $this;
    }

    /**
     * @return int
     */
    public function getProgressPercentage(): int
    {
        return round(($this->rowsDone / $this->totalRows) * 100);
    }
}