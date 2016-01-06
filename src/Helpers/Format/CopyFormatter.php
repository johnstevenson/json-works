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
* A class to return an unreferenced copy of data, with an optional callback
*/
class CopyFormatter extends BaseFormatter
{
    /**
    * Returns an unreferenced copy of the data
    *
    * @param mixed $data
    * @param callable|null $callback Optional callback function
    * @return mixed
    */
    public function run($data, $callback = null)
    {
        if (!$callback) {
            if (is_scalar($data)) {
                return $data;
            }
        }

        return $this->copy($data, $callback);
    }

    /**
    * Returns an unreferenced copy of the data
    *
    * @param mixed $data
    * @param callable|null $callback
    * @return mixed
    */
    protected function copy($data, $callback)
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
            $isObject = $this->isObject($key, $isObject);
            $result[$key] = $this->copy($value, $callback);
        }

        return $this->formatContainer($result, $isObject);
    }
}
