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

/**
* A class for manipulating array, object or json data
*/
class Formatter
{
    /**
    * Returns an unreferenced copy of the data
    *
    * @param mixed $data
    * @param callable|null $callback Optional callback function
    * @return mixed
    */
    public function copyData($data, $callback = null)
    {
        if ($callback) {
            $data = call_user_func_array($callback, array($data));
        }

        if ($this->isContainer($data, $isObject)) {
            return $this->copyContainer($data, $isObject, $callback);
        }

        return $data;
    }

    /**
    * Recursively copies an object or array
    *
    * @param object|array $data The data to copy
    * @param bool $isObject If the data is an object
    * @param callable|null $callback An optional callback
    * @return object|array
    */
    protected function copyContainer($data, $isObject, $callback)
    {
        $result = array();

        foreach ($data as $key => $value) {
            $isObject = $isObject ?: is_string($key);
            $result[$key] = $this->copyData($value, $callback);
        }

        return $isObject ? (object) $result: $result;
    }

    /**
    * Returns true if the data is an object or array
    *
    * @param mixed $data The data to check
    * @param bool $isObject True if the data is an object
    * @return bool
    */
    protected function isContainer($data, &$isObject)
    {
        return ($isObject = is_object($data)) || is_array($data);
    }
}
