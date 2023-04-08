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

/**
* A class for creating and manipulating JSON Pointers
*/
class Tokenizer
{
    /**
    * Adds a token to an existing JSON Pointer
    *
    * @api
    * @param string $pointer The existing JSON Pointer
    * @param string $token The token to add
    * @return string The new JSON Pointer
    */
    public function add(string $pointer, string $token): string
    {
        $encoded = $this->encodeToken($token);

        // not sure about this bit, re empty string keys
        if (Utils::stringNotEmpty($encoded)) {
            $encoded = '/'.$encoded;
        }

        return $pointer.$encoded;
    }

    /**
    * Splits a JSON Pointer into individual tokens
    *
    * @api
    * @param string $pointer The JSON Pointer to split
    * @param array<string> $tokens Placeholder for decoded tokens
    * @return bool If the pointer is valid
    */
    public function decode(string $pointer, &$tokens): bool
    {
        if (Utils::stringNotEmpty($pointer) && $pointer[0] !== '/') {
            return false;
        }

        $tokens = explode('/', $pointer);
        array_shift($tokens);

        foreach ($tokens as $key => $value) {
            $tokens[$key] = $this->processToken($value);
        }

        return true;
    }

    /**
    * Creates a JSON Pointer from a string or an array of tokens
    *
    * @api
    * @param string|array<string> $tokens
    * @return string The encoded JSON Pointer
    */
    public function encode($tokens): string
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
    public function encodeToken(string $token): string
    {
        return str_replace('/', '~1', str_replace('~', '~0', strval($token)));
    }

    /**
    * Returns a correctly formatted token
    *
    * @param string $token
    * @return string
    */
    protected function processToken(string $token): string
    {
        return str_replace('~0', '~', str_replace('~1', '/', $token));
    }
}
