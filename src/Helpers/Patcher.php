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

use InvalidArgumentException;
use JohnStevenson\JsonWorks\Helpers\Finder;
use JohnStevenson\JsonWorks\Helpers\Patch\Builder;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;

/**
* A class for building json
*/
class Patcher
{
    /**
    * @var Patch\Builder
    */
    protected $builder;

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
    * @var bool
    */
    protected $jsonPatch;

    public function __construct($jsonPatch = false)
    {
        $this->jsonPatch = $jsonPatch;
        $this->builder = new Builder();
        $this->finder = new Finder();
    }

    public function add(&$data, $path, $value)
    {
        if (!$this->getElement($data, $path, $value, $target)) {
            return false;
        }

        if ($result = $this->addToData($value, $target)) {
            $data = $this->data;
        }

        return $result;
    }

    public function remove(&$data, $path)
    {
        if ($result = $this->find($data, $path, $target)) {

            if (0 === strlen($target->childKey)) {
                $data = null;
            } elseif (is_array($target->parent)) {
                array_splice($target->parent, (int) $target->childKey, 1);
            } else {
                unset($target->parent->{$target->childKey});
            }
        }

        return $result;
    }

    /**
    * Replaces an element if found
    *
    * @param mixed $data
    * @param string $path
    * @param mixed $value
    * @return bool If the element was replaced
    */
    public function replace(&$data, $path, $value)
    {
        if ($result = $this->find($data, $path, $target)) {
            $result = $this->addToData($value, $target);
        }

        return $result;
    }

    public function getError()
    {
        return $this->error;
    }

    protected function find(&$data, $path, &$target, $add = false)
    {
        // We won't need these assignments when we have refactored
        // the builder to return a value to add to the found element
        $this->data = $data;
        $this->element =& $this->data;

        $target = new Target($path, $this->error);

        if ($target->errorCode) {
            return false;
        }

        if ($add) {
            $this->element =& $this->finder->get($this->element, $target);
        } else {
            $this->element =& $this->finder->get($data, $target);
        }


        return $target->found;
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

    protected function addToData($value, Target $target)
    {
        if ($target->type === Target::TYPE_ARRAY) {
            array_splice($this->element, $target->key, 0, [$value]);
        } elseif ($target->type === Target::TYPE_OBJECT) {
            $this->element->{$target->key} = $value;
        } elseif ($this->data === $this->element) {
            return $this->addToRoot($value);
        } else {
            $this->element = $value;
        }

        return true;
    }

    protected function getElement(&$data, $path, &$value, &$target)
    {
        if ($result = $this->find($data, $path, $target, true)) {

            if (is_array($target->parent)) {
                $target->setArray($target->childKey);
                $this->element =& $target->parent;
            }

            return true;
        }

        if ($this->jsonPatch && count($target->tokens) > 1) {
            return false;
        }

        return $this->buildElement($target);
    }

    protected function buildElement(Target $target)
    {
        $result = true;

        try {
            $this->element =& $this->builder->add($this->element, $target);
        } catch (InvalidArgumentException $e) {
            $this->error = $e->getMessage();
            $result = false;
        }

        return $result;
    }
}
