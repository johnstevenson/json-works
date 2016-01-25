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
    protected $element;

    public function &add(&$data, Target $target)
    {
        $this->element =& $data;
        $this->target =& $target;

        if (is_null($this->element) && !empty($this->target->tokens)) {
            $this->addContainer($this->target->tokens[0]);
        }

        $this->processTokens($this->target->tokens);

        return $this->element;
    }

    protected function processTokens(array &$tokens)
    {
        while (!empty($tokens)) {

            $key = array_shift($tokens);

            if (!empty($tokens)) {
                $this->createElement($key, $tokens[0]);

            } else {
                // No tokens left so set target type from the key
                $this->setTarget($key);
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
                $this->target->setError(Error::ERR_KEY_INVALID);
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
        if (is_array($this->element)) {

            $this->checkArrayKey($this->element, $key, $index);
            $this->target->setArray($index);
        } else {
            $this->target->setObject($key);
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
            $this->target->setError(Error::ERR_KEY_INVALID);
            throw new InvalidArgumentException($this->target->error);
        }
    }
}
