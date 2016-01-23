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
use JohnStevenson\JsonWorks\Helpers\Patch\Target;

class Builder
{
    /**
    * @var mixed
    */
    protected $element;

    public function &add(&$data, Target &$target)
    {
        $this->element =& $data;

        if (is_null($this->element) && !empty($target->tokens)) {
            $this->addContainer($target->tokens[0]);
        }

        $this->processTokens($target);

        return $this->element;
    }

    protected function processTokens(Target &$target)
    {
        while (!empty($target->tokens)) {

            $key = array_shift($target->tokens);

            if (!empty($target->tokens)) {
                $this->createElement($key, $target->tokens[0]);

            } else {
                // No tokens left so set target type from the key
                $this->setTarget($target, $key);
            }
        }
    }

    protected function addContainer($token)
    {
        $this->element = $this->isPushKey($token) ? [] : new \stdClass();
    }

    protected function createElement($key, $containerKey)
    {
        if (is_array($this->element)) {

            if (!$this->isPushKey($key)) {
                throw new InvalidArgumentException('Invalid array key');
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

    protected function setTarget(Target &$target, $key)
    {
        if (is_array($this->element)) {

            if (!$this->checkArrayKey($this->element, $key, $index)) {
                throw new InvalidArgumentException('Invalid array key');
            }

            $target->setArray($index);

        } else {
            $target->setObject($key);
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

        return (bool) $result;
    }
}
