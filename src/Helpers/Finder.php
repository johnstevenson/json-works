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

use JohnStevenson\JsonWorks\Helpers\Tokenizer;

/**
* A class for finding a value using JSON Pointers
*/
class Finder
{
    /**
    * @var Tokenizer
    */
    protected $tokenizer;

    /**
    * @var mixed
    */
    protected $element;

    /**
    * @var mixed
    */
    protected $parent;

    /**
    * @var string
    */
    protected $lastKey;

    public function __construct()
    {
        $this->tokenizer = new Tokenizer();
    }

    /**
    * Returns true if an element is found
    *
    * @param string $path
    * @param mixed $data
    * @param mixed $element Set by method if found
    * @return bool
    */
    public function find($path, $data, &$element)
    {
        $this->get($path, $data, $tokens, $found);

        if ($found) {
            $element = $this->element;
        }

        return $found;
    }

    /**
    * Searches for and returns a reference to an element
    *
    * @api
    * @param string $path
    * @param mixed $data
    * @param array $tokens Set to last known tokens on failure
    * @param bool $found Set by method
    * @return mixed A reference to the found element
    */
    public function &get($path, &$data, &$tokens, &$found)
    {
        $this->element =& $data;

        $tokens = $this->tokenizer->decode($path);
        $found = empty($tokens);

        if (!$found) {
            $this->search($tokens, $found);
        }

        return $this->element;
    }

    /**
    * Searches for and returns a reference to a parent element
    *
    * @api
    * @param string $path
    * @param mixed $data
    * @param bool $found Set by method
    * @param mixed $lastKey Set by method
    * @return mixed A reference to the parent element
    */
    public function &getParent($path, &$data, &$found, &$lastKey)
    {
        $this->parent =& $data;
        $this->lastKey = '';

        $this->get($path, $data, $tokens, $found);

        if ($found) {
            $lastKey = $this->lastKey;
        }

        return $this->parent;
    }

    /**
    * Returns true if the token is a valid array key
    *
    * @api
    * @param string $token
    * @param mixed $index Set to an integer on success
    */
    public function isArrayKey($token, &$index)
    {
        $index = null;

        if (preg_match('/^((0)|([1-9]\d*))$/', $token)) {
            $index = (int) $token;
        }

        return $index !== null;
    }

    /**
    * Searches through the data
    *
    * @param array $tokens Set and reduced by method
    * @param bool $found Set by method
    */
    protected function search(&$tokens, &$found)
    {
        while (!empty($tokens)) {
            $token = $tokens[0];

            if (count($tokens) === 1) {
                $this->parent =& $this->element;
                $this->lastKey = $token;
            }

            if (!$found = $this->findContainer($token)) {
                break;
            }

            array_shift($tokens);
        }
    }

    /**
    * Returns true if a token is found at the current data root
    *
    * A reference to the value is placed in $this->element
    *
    * @param string $token
    * @return bool
    */
    protected function findContainer($token)
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
}
