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
* A class to return an unreferenced copy of the data, with an optional callback
*/
class Copier extends BaseFormat
{
    /**
    * Returns an unreferenced copy of the data
    *
    * @internal
    * @param mixed $data
    * @param callable|null $callback Optional callback function
    * @return mixed
    */
    public function run($data, $callback = null)
    {
        if ($callback) {
            $data = call_user_func_array($callback, array($data));
        }

        if ($this->isContainer($data, $object)) {
            return $this->copyContainer($data, $object, $callback);
        }

        return $data;
    }

    /**
    * Recursively copies an object or array
    *
    * @param object|array $data The data to copy
    * @param bool $object Whether the result should be an object
    * @param callable|null $callback An optional callback
    * @return object|array An unreferenced copy
    */
    protected function copyContainer($data, $object, $callback)
    {
        $result = [];

        foreach ($data as $key => $value) {
            $result[$key] = $this->run($value, $callback);
        }

        return $this->formatContainer($result, $object);
    }
}
