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

    protected function initData()
    {
        if (!empty($this->target->tokens)) {

            $key = $this->target->tokens[0];

            if (is_null($this->data)) {
                // 1st pass: creating a root container
                // 2nd pass: recursing
                $this->addContainer($key);
            } else {
                $this->setTarget($key);
                $this->data = null;
                array_shift($this->target->tokens);
                $this->initData();
            }
        }
    }

    protected function processTokens(array &$tokens, $value)
    {
        while (!empty($tokens)) {

            $key = array_shift($tokens);

            if (!empty($tokens)) {
                $this->createElement($key, $tokens[0]);

            } else {
                // No tokens left so set the value
                $this->setValue($key, $value);
            }
        }
    }

    protected function addContainer($key)
    {
        $this->element = $this->isPushKey($key) ? [] : new \stdClass();
    }

    protected function createElement($key, $containerKey)
    {
        if (is_array($this->element)) {

            if (!$this->isPushKey($key)) {
                $this->target->setError(Error::ERR_PATH_KEY);
                throw new InvalidArgumentException($this->target->error);
            }

            $this->element[0] = null;
            $this->element = &$this->element[0];
            $this->addContainer($containerKey);

        } else {

            $this->element->$key = null;
            $this->element = &$this->element->$key;
            $this->addContainer($containerKey);
        }
    }

    protected function setTarget($key)
    {
        if (is_array($this->data)) {
            $this->checkArrayKey($this->data, $key, $index);
            $this->target->setArray($index);

        } else {
            $this->target->setObject($key);
        }
    }

    protected function setValue($key, $value)
    {
        if (is_array($this->element)) {
            $this->checkArrayKey($this->element, $key, $index);
            $this->element[$index] = $value;

        } else {
            $this->element->$key = $value;
        }
    }

    protected function isPushKey($key)
    {
        return (bool) preg_match('/^((-)|(0))$/', $key);
    }

    protected function checkArrayKey(array $array, $key, &$index)
    {
        if ($result = preg_match('/^(?:(-)|(0)|([1-9]\d*))$/', $key, $matches)) {

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
