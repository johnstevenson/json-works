<?php
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Helpers;

use JohnStevenson\JsonWorks\Helpers\Error;

/**
* A class for formatting and setting error messages
*/
class Error
{
    const ERR_SUCCESS = 0;
    const ERR_NOT_FOUND = 1;
    const ERR_PATH_KEY = 2;
    const ERR_BAD_INPUT = 3;
    const ERR_VALIDATE = 4;

    /**
    * Returns a formatted error message
    *
    * @api
    * @param integer $code
    * @param string $msg
    * @return string
    */
    public function get($code, $msg = '')
    {
        if ($code === self::ERR_VALIDATE) {
            return sprintf('ERR_VALIDATE: %s', $msg);
        }

        $error = $this->codeToString($code, $matched);

        if ($msg) {
            $format = $matched ? ' [%s]' : ' %s';
            $error .= sprintf($format, $msg);
        }

        return $error;
    }

    /**
    * Formats and returns an error message
    *
    * @param integer $code
    * @param $matched Set by method
    * @return string
    */
    protected function codeToString($code, &$matched)
    {
        $matched = true;

        switch ($code) {
            case self::ERR_NOT_FOUND:
                $title = 'ERR_NOT_FOUND';
                $msg = 'Unable to find resource';
                break;
            case self::ERR_PATH_KEY:
                $title = 'ERR_PATH_KEY';
                $msg = 'Invalid path key';
                break;
            case self::ERR_BAD_INPUT:
                $title = 'ERR_BAD_INPUT';
                $msg = 'Invalid input';
                break;
            default:
                $title = 'ERR_UNKNOWN';
                $msg = 'An error occurred';
                $matched = false;
        }

        return sprintf('%s: %s', $title, $msg);
    }
}
