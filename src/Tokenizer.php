<?php declare(strict_types=1);

/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks;

use JohnStevenson\JsonWorks\Helpers\Utils;

/**
 * A class for creating and manipulating JSON Pointers.
 * @api
 */
class Tokenizer
{
    /**
    * Adds a token to an existing JSON Pointer
    *
    * @param string $pointer The existing JSON Pointer
    * @param string $token The token to add
    * @return string The new JSON Pointer
    */
    public function add(string $pointer, string $token): string
    {
        $encoded = $this->encodeToken($token);

        return $pointer.'/'.$encoded;
    }

    /**
    * Splits a JSON Pointer into individual tokens
    *
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
    * @param string|array<string> $tokens
    * @return string The encoded JSON Pointer
    */
    public function encode($tokens): string
    {
        $result = '';
        foreach ((array) $tokens as $index => $value) {
            // skip empty first token
            if ($index === 0 && Utils::stringIsEmpty($value)) {
                continue;
            }
            $result = $this->add($result, $value);
        }

        return $result;
    }

    /**
    * Encodes a JSON Pointer token
    *
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
    private function processToken(string $token): string
    {
        return str_replace('~0', '~', str_replace('~1', '/', $token));
    }
}
