<?php declare(strict_types=1);

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
    * The $asObject param is set and reports if $data is either an object or an
    * associative array
    *
    * @param object|array<mixed>|mixed $data The data to check
    */
    protected function isContainer($data, ?bool &$asObject): bool
    {
        if ($asObject = is_object($data)) {
            return true;
        }

        if (is_array($data)) {
            $asObject = $this->isAssociative($data);
            return true;
        }

        return false;
    }

    /**
    * Determines if an array is associative
    *
    * @param array<mixed> $data
    */
    protected function isAssociative(array $data): bool
    {
        if (function_exists('array_is_list')) {
            return !array_is_list($data);
        }

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
    * @param object|array<mixed> $data
    * @return object|array<mixed>
    */
    protected function formatContainer($data, bool $asObject)
    {
        return $asObject ? (object) $data: $data;
    }
}
