<?php
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Helpers\Patch;

use InvalidArgumentException;
use JohnStevenson\JsonWorks\Helpers\Error;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;

class Builder
{
    /**
    * @var Target
    */
    protected $target;

    /**
    * @var mixed
    */
    protected $data;

    /**
    * @var mixed
    */
    protected $element;

    /**
    * Returns a new element to be added to the data
    *
    * @param Target $target
    * @param mixed $value
    * @return mixed
    */
    public function make(Target $target, $value)
    {
        $this->data = $target->element;
        $this->element =& $this->data;
        $this->target = $target;

        $this->initData();

        if (empty($target->tokens)) {
            $this->data = $value;
        } else {
            $this->processTokens($target->tokens, $value);
        }

        return $this->data;
    }

    /**
    * Initializes the data    *
    */
    protected function initData()
    {
        if (!empty($this->target->tokens)) {

            $key = $this->target->tokens[0];

            if (is_null($this->data)) {
                // 1st pass: creating a root container
                // 2nd pass: recursing from statement below
                $this->createContainer($key);
            } else {
                $this->setTarget($key);
                $this->data = null;
                array_shift($this->target->tokens);
                $this->initData();
            }
        }
    }

    /**
    * Builds new elements from the remaining tokens
    *
    * @param array $tokens
    * @param mixed $value The value to add to the final element
    */
    protected function processTokens(array $tokens, $value)
    {
        while (!empty($tokens)) {

            $key = array_shift($tokens);

            if (!empty($tokens)) {
                $this->addElement($key, $tokens[0]);

            } else {
                // No tokens left so set the value
                $this->setValue($key, $value);
            }
        }
    }

    /**
    * Creates a new object or array
    *
    * The new container is created on the current root element and its type
    * depends on the nature of the key.
    *
    * @param string $key
    */
    protected function createContainer($key)
    {
        if ($key === '-' || $key === '0') {
            $this->element = [];
        } else {
            $this->element = new \stdClass();
        }
    }

    /**
    * Adds a new container member to an object or array
    *
    * @param string $key
    * @param string $containerKey
    */
    protected function addElement($key, $containerKey)
    {
        if (is_array($this->element)) {
            $this->element[0] = null;
            $this->element =& $this->element[0];

        } else {
            $this->element->$key = null;
            $this->element =& $this->element->$key;
        }

        $this->createContainer($containerKey);
    }

    /**
    * Sets external target data for incorporating the data
    *
    * @param string $key
    */
    protected function setTarget($key)
    {
        if (is_array($this->data)) {
            $this->checkArrayKey($this->data, $key, $index);
            $this->target->setArray($index);

        } else {
            $this->target->setObject($key);
        }
    }

    /**
    * Sets an array item or an object property
    *
    * @param string $key
    * @param mixed $value
    */
    protected function setValue($key, $value)
    {
        if (is_array($this->element)) {
            $this->checkArrayKey($this->element, $key, $index);
            $this->element[$index] = $value;

        } else {
            $this->element->$key = $value;
        }
    }

    /**
    * Checks if an array key is valid and sets its index
    *
    * @param array $array
    * @param string $key
    * @param integer $index Set by method
    * @throws InvalidArgumentException
    */
    protected function checkArrayKey(array $array, $key, &$index)
    {
        if ($result = preg_match('/^(?:(-)|(0)|([1-9]\d*))$/', $key)) {

            if ($key === '-') {
                $index = count($array);
            } else {
                $index = (int) $key;
                $result = $index <= count($array);
            }
        }

        if (!$result) {
            $this->target->setError(Error::ERR_PATH_KEY);
            throw new InvalidArgumentException($this->target->error);
        }
    }
}
