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
* A static class containing methods for creating JSON Pointers
*/
class Path
{
    /**
    * Adds a token to an existing JSON Pointer
    *
    * @param string $path The existing JSON Pointer
    * @param string $key The token to add
    * @return string The new JSON Pointer
    */
    public static function add($path, $key)
    {
        if (strlen($encoded = static::encodeKey($key))) {
            $encoded = '/'.$encoded;
        }

        return $path.$encoded;
    }

    /**
    * Splits a JSON Pointer into individual tokens
    *
    * @param string $path The JSON Pointer to split
    * @return array The decoded tokens
    */
    public static function decode($path)
    {
        $keys = explode('/', $path);
        array_shift($keys);

        foreach ($keys as &$value) {
            $value = str_replace('~0', '~', str_replace('~1', '/', $value));
        }

        return $keys;
    }

    /**
    * Creates a JSON Pointer from a string or an array of tokens
    *
    * @param string|array $keys
    * @return string The encoded JSON Pointer
    */
    public static function encode($keys)
    {
        $result = '';
        foreach ((array) $keys as $value) {
            $result = static::add($result, $value);
        }

        return $result;
    }

    /**
    * Encodes a JSON Pointer token
    *
    * @param string $key
    * @return string The encoded JSON Pointer
    */
    public static function encodeKey($key)
    {
        return str_replace('/', '~1', str_replace('~', '~0', strval($key)));
    }
}
