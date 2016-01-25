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

use JohnStevenson\JsonWorks\Helpers\Error;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;

/**
* A class for finding a value using JSON Pointers
*/
class Finder
{
    /**
    * @var Patch\Target
    */
    protected $target;

    /**
    * @var mixed
    */
    protected $element;

    /**
    * Returns true if an element is found
    *
    * @api
    * @param string $path
    * @param mixed $data
    * @param mixed $element Set by method if found
    * @return bool
    */
    public function find($path, $data, &$element)
    {
        $target = new Target($path, $dummy);
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
    public function &get(&$data, Target $target)
    {
        $this->element =& $data;
        $this->target =& $target;

        if (!empty($this->target->tokens)) {
            $found = $this->search($this->target->tokens);
            $this->target->setFound($found);
        }

        return $this->element;
    }

    /**
    * Returns true if the element is found
    *
    * @param Target $target Modified by method
    * @return bool
    */
    protected function search(array &$tokens)
    {
        // tokens is guaranteed not empty
        while (!empty($tokens)) {
            $token = $tokens[0];

            if (count($tokens) === 1) {
                $this->target->parent =& $this->element;
                $this->target->childKey = $token;
            }

            if (!$this->findContainer($token)) {
                return false;
            }

            array_shift($tokens);
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

        if (is_object($this->element)) {
            $found = $this->findObject($token);
        } elseif (is_array($this->element)) {
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
        if (!$this->isArrayKey($token, $index)) {
            $this->target->setError(Error::ERR_KEY_INVALID);
            return false;
        }

        if ($result = array_key_exists($index, $this->element)) {
            $this->element = &$this->element[$index];
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
    * @api
    * @param string $token
    * @param mixed $index Set to an integer on success
    */
    protected function isArrayKey($token, &$index)
    {
        if ($result = preg_match('/^((0)|([1-9]\d*))$/', $token)) {
            $index = (int) $token;
        }

        return (bool) $result;
    }
}
