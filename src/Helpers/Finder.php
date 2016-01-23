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
use JohnStevenson\JsonWorks\Helpers\Patch\Target;

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
    * Returns true if an element is found
    *
    * @param string $path
    * @param mixed $data
    * @param mixed $element Set by method if found
    * @return bool
    */
    public function find($path, $data, &$element)
    {
        $target = new Target($path);
        $this->get($data, $target);

        if ($target->found) {
            $element = $this->element;
        }

        return $target->found;
    }

    /**
    * Searches for and returns a reference to an element
    *
    * @api
    * @param mixed $data
    * @param Target $target Modified by method
    * @return mixed A reference to the found element
    */
    public function &get(&$data, Target &$target)
    {
        $this->element =& $data;

        if (!$target->found) {
            $target->found = $this->search($target);
        }

        return $this->element;
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
        if ($result = preg_match('/^((0)|([1-9]\d*))$/', $token)) {
            $index = (int) $token;
        }

        return (bool) $result;
    }

    /**
    * Returns true if the element is found
    *
    * @param Target $target Modified by method
    * @return bool
    */
    protected function search(Target &$target)
    {
        // Tokens is guaranteed not empty
        while (!empty($target->tokens)) {
            $token = $target->tokens[0];

            if (count($target->tokens) === 1) {
                $target->parent =& $this->element;
                $target->childKey = $token;
                //$target->setParent($this->element, $token);
            }

            if (!$this->findContainer($token)) {
                return false;
            }

            array_shift($target->tokens);
        }

        return true;
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
