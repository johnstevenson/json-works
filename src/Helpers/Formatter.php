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
    public function copyData($data, $callback = null)
    {
        if ($callback) {
            $data = call_user_func_array($callback, array($data));
        }

        if (($object = is_object($data)) || is_array($data)) {

            $result = array();

            foreach ($data as $key => $value) {
                $object = $object ?: is_string($key);
                $result[$key] = $this->copyData($value, $callback);
            }

            $result = $object ? (object) $result: $result;

        } else {
            $result = $data;
        }

        return $result;
    }
}
