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
* A class for finding a value using JSON Pointers
*/
class Finder
{
    /**
    * @var mixed
    */
    protected $element;

    /**
    * Searches for and returns a reference to an element
    *
    * @param mixed $data
    * @param array $tokens Set to last known token on failure
    * @param bool $found Set by method
    * @return mixed A reference to the found element
    */
    public function &get(&$data, &$tokens, &$found)
    {
        $this->element =& $data;

        while (!empty($tokens)) {
            $token = $tokens[0];

            if (!$found = $this->find($token)) {
                break;
            }

            array_shift($tokens);
        }

        return $this->element;
    }

    /**
    * Returns true if a token is found at the data root
    *
    * A reference to the value is placed in $this->element
    *
    * @param string $token
    * @return bool
    */
    protected function find($token)
    {
        $found = false;
        $type = gettype($this->element);

        if ('object' === $type) {
            $found = $this->findObject($token);
        } elseif ('array' === $type) {
            $found = $this->findArray($token);
        }

        return $found;
    }

    /**
    * Returns true if the token is an array key
    *
    * Sets $this->element to reference the value
    * @param string $token
    * @return bool
    */
    protected function findArray($token)
    {
        if ($result = $this->isArrayKey($token, $index)) {
            if ($result = array_key_exists($index, $this->element)) {
                $this->element = &$this->element[$index];
            }
        }

        return $result;
    }

    /**
    * Returns true if the token is an object property key
    *
    * Sets $this->element to reference the value
    * @param string $token
    * @return bool
    */
    protected function findObject($token)
    {
        if ($result = property_exists($this->element, $token)) {
            $this->element = &$this->element->$token;
        }

        return $result;
    }

    /**
    * Returns true if the token is a valid array key
    *
    * @param string $token
    * @param mixed $index Set to an integer on success
    */
    protected function isArrayKey($token, &$index)
    {
        $index = null;

        if (preg_match('/^0*\d+$/', $token)) {
            $index = (int) $token;
        }

        return $index !== null;
    }
}
