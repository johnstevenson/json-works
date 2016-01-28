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
    const ERR_NOT_FOUND = 'ERR_NOT_FOUND';
    const ERR_PATH_KEY = 'ERR_PATH_KEY';
    const ERR_BAD_INPUT = 'ERR_BAD_INPUT';
    const ERR_VALIDATE = 'ERR_VALIDATE';

    /**
    * Returns a formatted error message
    *
    * @api
    * @param string $code
    * @param string $msg
    * @return string
    */
    public function get($code, $msg)
    {
        if ($caption = $this->codeGetCaption($code)) {
            $error = sprintf('%s: %s [%s]', $code, $caption, $msg);
        } else {
            $error = sprintf('%s: %s', $code, $msg);
        }

        return $error;
    }

    /**
    * Returns an error caption
    *
    * @param string $code
    * @return string
    */
    protected function codeGetCaption($code)
    {
        $result = '';

        switch ($code) {
            case self::ERR_NOT_FOUND:
                $result = 'Unable to find resource';
                break;
            case self::ERR_PATH_KEY:
                $result = 'Invalid path key';
                break;
            case self::ERR_BAD_INPUT:
                $result = 'Invalid input';
                break;
        }

        return $result;
    }
}
