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

use JohnStevenson\JsonWorks\Helpers\Finder;

/**
* A class for building json
*/
class Builder
{
    /**
    * @var Finder
    */
    protected $finder;

    /**
    * @var object|array|null
    */
    protected $data;

    /**
    * @var mixed
    */
    protected $element;

    /**
    * @var string
    */
    protected $error;

    /**
    * @var string
    */
    protected $newProperty;

    /**
    * @var bool
    */
    protected $arrayPush;

    public function __construct()
    {
        $this->finder = new Finder();
    }

    public function add(&$data, $path, $value)
    {
        $this->initAdd($data);

        if (!$this->addElements($path)) {
            return false;
        }

        if ($result = $this->addToData($value)) {
            $data = $this->data;
        }

        return $result;
    }

    public function remove(&$data, $path)
    {
        $parent =& $this->finder->getParent($path, $data, $found, $lastKey);

        if ($found) {

            if (0 === strlen($lastKey)) {
                $data = null;
            } elseif (is_array($parent)) {
                array_splice($parent, (int) $lastKey, 1);
            } elseif (is_object($parent)) {
                unset($parent->$lastKey);
            }
        }

        return $found;
    }

    public function getError()
    {
        return $this->error;
    }

    protected function initAdd($data)
    {
        $this->data = $data;
        $this->element =& $this->data;
        $this->error = '';
        $this->newProperty = '';
        $this->arrayPush = false;
    }

    protected function addToRoot($value)
    {
        if (!(is_object($value) || is_array($value))) {
            $this->error = 'Value must be an object or array';
            return false;
        }

        $this->element = $value;

        return true;
    }

    protected function addToData($value)
    {
        if ($this->arrayPush) {
            array_push($this->element, $value);
        } elseif (strlen($this->newProperty)) {
            $key = $this->newProperty;
            $this->element->$key = $value;
        } elseif ($this->data === $this->element) {
            return $this->addToRoot($value);
        } else {
            $this->element = $value;
        }

        return true;
    }

    protected function addElements($path)
    {
        $this->element =& $this->finder->get($path, $this->element, $tokens, $found);

        if (is_null($this->element) && !empty($tokens)) {
            $this->addContainer($tokens[0]);
        }

        while (!empty($tokens)) {

            $key = array_shift($tokens);

            if (!empty($tokens)) {

                if (is_array($this->element)) {

                    if (!$this->isPushKey($key)) {
                        $this->error = 'Invalid array key';
                        return false;
                    }

                    $this->element[0] = null;
                    $this->element = &$this->element[0];
                    $this->addContainer($tokens[0]);

                } else {

                    $this->element->$key = null;
                    $this->element = &$this->element->$key;
                    $this->addContainer($tokens[0]);
                }

            } else {
                 # no more pointers. First check for array with final array key

                if (is_array($this->element)) {

                    if ($this->isArrayKey($key, $index)) {
                        $index = is_int($index) ? $index : count($this->element);
                        $this->arrayPush = $index === count($this->element);
                    }

                    if (!$this->arrayPush) {
                        $this->error = 'Bad array index';
                        return false;
                    }

                } else {
                    $this->newProperty = $key;
                }
            }
        }

        return true;
    }

    protected function addContainer($token)
    {
        $isArray = $this->isPushKey($token);
        $this->element = $isArray ? [] : new \stdClass();
    }

    protected function isPushKey($value)
    {
        return (bool) preg_match('/^((-)|(0))$/', $value);
    }

    protected function isArrayKey($value, &$index)
    {
        if ($value === '-') {
            $index = '-';
            return true;
        }

        return $this->finder->isArrayKey($value, $index);
    }
}
