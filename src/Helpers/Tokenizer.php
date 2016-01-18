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
* A class for creating and manipulating JSON Pointers
*/
class Tokenizer
{
    /**
    * Adds a token to an existing JSON Pointer
    *
    * @api
    * @param string $path The existing JSON Pointer
    * @param string $token The token to add
    * @return string The new JSON Pointer
    */
    public function add($path, $token)
    {
        if (strlen($encoded = $this->encodeToken($token))) {
            $encoded = '/'.$encoded;
        }

        return $path.$encoded;
    }

    /**
    * Splits a JSON Pointer into individual tokens
    *
    * @api
    * @param string $path The JSON Pointer to split
    * @return array The decoded tokens
    */
    public function decode($path)
    {
        $tokens = explode('/', $path);
        array_shift($tokens);

        foreach ($tokens as &$value) {
            $value = str_replace('~0', '~', str_replace('~1', '/', $value));
        }

        return $tokens;
    }

    /**
    * Creates a JSON Pointer from a string or an array of tokens
    *
    * @api
    * @param string|array $tokens
    * @return string The encoded JSON Pointer
    */
    public function encode($tokens)
    {
        $result = '';
        foreach ((array) $tokens as $value) {
            $result = $this->add($result, $value);
        }

        return $result;
    }

    /**
    * Encodes a JSON Pointer token
    *
    * @api
    * @param string $token
    * @return string The encoded JSON Pointer
    */
    public function encodeToken($token)
    {
        return str_replace('/', '~1', str_replace('~', '~0', strval($token)));
    }
}
