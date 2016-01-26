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
use JohnStevenson\JsonWorks\Helpers\Error;
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

        return $this->addData($target, $value);
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
        if (!$this->find($data, $path, $target)) {
            return false;
        }

        return $this->addData($target, $value);
    }

    public function getError()
    {
        return $this->error;
    }

    protected function addData(Target $target, $value)
    {
        if ($target->type === Target::TYPE_ARRAY) {
            array_splice($target->element, $target->key, 0, [$value]);

        } elseif ($target->type === Target::TYPE_OBJECT) {
            $target->element->{$target->key} = $value;

        } elseif (!$target->path) {
            return $this->addRoot($target, $value);

        } else {
            $target->element = $value;
        }

        return true;
    }

    protected function addRoot(Target $target, $value)
    {
        if (!(is_object($value) || is_array($value))) {
            $target->setError(Error::ERR_BAD_VALUE);
            return false;
        }

        $target->element = $value;

        return true;
    }

    protected function find(&$data, $path, &$target)
    {
        $target = new Target($path, $this->error);

        return $this->finder->get($data, $target);
    }

    protected function getElement(&$data, $path, &$value, &$target)
    {
        if ($this->find($data, $path, $target)) {

            if (is_array($target->parent)) {
                $target->setArray($target->childKey);
                $target->element =& $target->parent;
            }

            return true;
        }

        return $this->buildElement($target, $value);
    }

    protected function buildElement(Target $target, &$value)
    {
        if ($target->invalid || ($this->jsonPatch && count($target->tokens) > 1)) {
            return false;
        }

        $result = true;

        try {
            $value = $this->builder->make($target, $value);
        } catch (InvalidArgumentException $e) {
            $result = false;
        }

        return $result;
    }
}
