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
use JohnStevenson\JsonWorks\Helpers\Tokenizer;

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
    * @var Tokenizer
    */
    protected $tokenizer;

    /**
    * @var object|array|null
    */
    protected $data;

    /**
    * @var mixed
    */
    protected $element;

    /**
    * @var array
    */
    protected $tokens;

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
        $this->tokenizer = new Tokenizer();
    }

    public function add($data, $pointer, $value)
    {
        $this->init($data, $pointer);

        if (empty($this->tokens)) {
            return $this->addToRoot($value);
        }

        if (!$this->addElements()) {
            return false;
        }

        $this->addToData($value);

        return true;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getError()
    {
        return $this->error;
    }

    protected function init($data, $pointer)
    {
        $this->data = $data;
        $this->element =& $this->data;
        $this->tokens = $this->tokenizer->decode($pointer);
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

        $this->data = $value;

        return true;
    }

    protected function addToData($value)
    {
        if ($this->arrayPush) {
            array_push($this->element, $value);
        } elseif (strlen($this->newProperty)) {
            $key = $this->newProperty;
            $this->element->$key = $value;
        } else {
            $this->element = $value;
        }
    }

    protected function addElements()
    {
        $this->element =& $this->finder->get($this->element, $this->tokens, $found);

        if (is_null($this->element)) {
            $this->addContainer($this->tokens[0]);
        }

        while (!empty($this->tokens)) {

            $key = array_shift($this->tokens);

            if (!empty($this->tokens)) {

                if (is_array($this->element)) {

                    if (!$this->isPushKey($key)) {
                        $this->error = 'Invalid array key';
                        return false;
                    }

                    $this->element[0] = null;
                    $this->element = &$this->element[0];
                    $this->addContainer($this->tokens[0]);

                } else {

                    $this->element->$key = null;
                    $this->element = &$this->element->$key;
                    $this->addContainer($this->tokens[0]);
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
