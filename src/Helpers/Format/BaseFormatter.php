<?php
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Helpers\Format;

/**
* A base class to be extended by data formatters
*/
class BaseFormatter
{
    /**
    * Returns true if the data is an object or array
    *
    * @param mixed $data The data to check
    * @param bool $isObject Set by the method
    * @return bool
    */
    protected function isContainer($data, &$isObject)
    {
        return ($isObject = is_object($data)) || is_array($data);
    }

    /**
    * Returns true if already an object or the array key is a string
    *
    * @param mixed $arrayKey The array key to check
    * @param bool $isObject If the data is already an object
    * @return bool
    */
    protected function isObject($arrayKey, $isObject)
    {
        return $isObject ?: is_string($arrayKey);
    }

    /**
    * Casts a value as an object if it has been classified as such
    *
    * @param object|array $data
    * @param bool $isObject
    * @return object|array
    */
    protected function formatContainer($data, $isObject)
    {
        return $isObject ? (object) $data: $data;
    }
}
