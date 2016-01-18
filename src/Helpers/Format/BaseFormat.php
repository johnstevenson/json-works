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
class BaseFormat
{
    /**
    * Returns true if the data is an object or array
    *
    * The $object param is set and reports if $data is either an object or an
    * associative array
    *
    * @param mixed $data The data to check
    * @param bool $object Set by the method
    * @return bool
    */
    protected function isContainer($data, &$object)
    {
        if ($object = is_object($data)) {
            return true;
        }

        if (is_array($data)) {
            $object = $this->isAssociative($data);
            return true;
        }

        return false;
    }

    /**
    * Determines if an array is associative
    *
    * @param array $data
    * @return bool
    */
    protected function isAssociative(array $data)
    {
        foreach ($data as $key => $value) {
            if ($key !== (int) $key) {
                return true;
            }
        }

        return false;
    }

    /**
    * Casts a value as an object if required
    *
    * @param object|array $data
    * @param bool $object
    * @return object|array
    */
    protected function formatContainer($data, $object)
    {
        return $object ? (object) $data: $data;
    }
}
