<?php declare(strict_types=1);

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
    public const ERR_NOT_FOUND = 'ERR_NOT_FOUND';
    public const ERR_PATH_KEY = 'ERR_PATH_KEY';
    public const ERR_BAD_INPUT = 'ERR_BAD_INPUT';
    public const ERR_VALIDATE = 'ERR_VALIDATE';

    /**
    * Returns a formatted error message
    *
    * @api
    */
    public function get(string $code, string $msg): string
    {
        $caption = $this->codeGetCaption($code);

        if ($caption !== null) {
            $error = sprintf('%s: %s [%s]', $code, $caption, $msg);
        } else {
            $error = sprintf('%s: %s', $code, $msg);
        }

        return $error;
    }

    /**
    * Returns an error caption
    */
    protected function codeGetCaption(string $code): ?string
    {
        $result = null;

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
