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

use JohnStevenson\JsonWorks\Helpers\Patch\Target;

class Builder
{
    /**
    * @var mixed
    */
    protected $element;

    public function &add($tokens, &$data, Target &$target)
    {
        $this->element =& $data;

        $this->addElements($tokens, $target);

        return $this->element;
    }

    protected function addElements($tokens, Target &$target)
    {
        if (is_null($this->element) && !empty($tokens)) {
            $this->addContainer($tokens[0]);
        }

        while (!empty($tokens)) {

            $key = array_shift($tokens);

            if (!empty($tokens)) {
                $this->createElement($tokens, $key);

            } else {
                $this->setTarget($target, $key);
            }
        }
    }

    protected function addContainer($token)
    {
        $isArray = $this->isPushKey($token);
        $this->element = $isArray ? [] : new \stdClass();
    }

    protected function createElement($tokens, $key)
    {
        if (is_array($this->element)) {

            if (!$this->isPushKey($key)) {
                $this->throwError('Invalid array key');
            }

            $this->element[0] = null;
            $this->element = &$this->element[0];
            $this->addContainer($tokens[0]);

        } else {

            $this->element->$key = null;
            $this->element = &$this->element->$key;
            $this->addContainer($tokens[0]);
        }
    }

    protected function setTarget(Target &$target, $key)
    {
        if (is_array($this->element)) {

            if ($this->isArrayKey($key, $index)) {
                $index = is_int($index) ? $index : count($this->element);

                if ($index === count($this->element)) {
                    $target->type = Target::TYPE_PUSH;
                }
            }

            if ($target->type !== Target::TYPE_PUSH) {
                $this->throwError('Bad array index');
            }

        } else {
            $target->type = Target::TYPE_PROPKEY;
            $target->propKey = $key;
        }
    }

    protected function isPushKey($value)
    {
        return (bool) preg_match('/^((-)|(0))$/', $value);
    }

    protected function isArrayKey($value, &$index)
    {
        if ($result = preg_match('/^(?:(-)|(0)|([1-9]\d*))$/', $value, $matches)) {
            $index = count($matches) > 2 ? (int) $value : $value;
        }

        return (bool) $result;
    }

    protected function throwError($msg)
    {
        throw new BuildException($msg);
    }
}
