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
    * @var string
    */
    protected $error;

    /**
    * @var bool
    */
    protected $jsonPatch;

    /**
    * Constructor
    *
    * If jsonPatch is set, elements will only be added to the root or an
    * existing element. See RFC 6902 (http://tools.ietf.org/html/rfc6902)
    *
    * @param mixed $jsonPatch
    */
    public function __construct($jsonPatch = false)
    {
        $this->jsonPatch = $jsonPatch;
        $this->builder = new Builder();
        $this->finder = new Finder();
    }

    /**
    * Adds an element to the data
    *
    * @param mixed $data
    * @param string $path
    * @param mixed $value
    * @return bool If the value was added
    */
    public function add(&$data, $path, $value)
    {
        if ($result = $this->getElement($data, $path, $value, $target)) {
            $this->addData($target, $value);
        }

        return $result;
    }

    /**
    * Removes an element if found
    *
    * @param mixed $data
    * @param string $path
    * @return bool If the data was removed
    */
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
            $this->addData($target, $value);
        }

        return $result;
    }

    public function getError()
    {
        return $this->error;
    }

    /**
    * Adds or modifies the data
    *
    * @param Target $target
    * @param mixed $value
    */
    protected function addData(Target $target, $value)
    {
        switch ($target->type) {
            case Target::TYPE_VALUE:
                $target->element = $value;
                break;
            case Target::TYPE_OBJECT:
                $target->element->{$target->key} = $value;
                break;
            case Target::TYPE_ARRAY:
                array_splice($target->element, $target->key, 0, [$value]);
                break;
        }
    }

    /**
    * Returns true if we can create a new element
    *
    * @param Target $target
    * @return bool
    */
    protected function canBuild(Target $target)
    {
        return !$target->invalid && !($this->jsonPatch && count($target->tokens) > 1);
    }

    /**
    * Returns true if an element is found
    *
    * @param mixed $data
    * @param string $path
    * @param Target $target
    */
    protected function find(&$data, $path, &$target)
    {
        $target = new Target($path, $this->error);

        return $this->finder->get($data, $target);
    }

    /**
    * Returns true if an element is found or built
    *
    * @param mixed $data
    * @param string $path
    * @param mixed $value
    * @param Target $target Set by method
    * @return bool
    */
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

    /**
    * Return true if a new value has been built
    *
    * @param Target $target
    * @param mixed $value
    * @return bool
    */
    protected function buildElement(Target $target, &$value)
    {
        if ($result = $this->canBuild($target)) {

            try {
                $value = $this->builder->make($target, $value);
            } catch (InvalidArgumentException $e) {
                $result = false;
            }
        }

        return $result;
    }
}
