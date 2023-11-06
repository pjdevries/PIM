<?php
/**
 * @package     calculated-4
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

namespace Pim\Api\Exception;

\defined('_JEXEC') or die;

class InvalidRequestMethodException extends \RuntimeException
{
    public function __construct($message = '', $code = 405, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}