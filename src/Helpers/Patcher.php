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
        $target = $this->init($data, $path);

        if (!$this->getElement($target)) {
            $this->error .= sprintf(': [%s]', $path);
            return false;
        }

        if ($result = $this->addToData($value, $target)) {
            $data = $this->data;
        }

        return $result;
    }

    public function remove(&$data, $path)
    {
        $target = $this->init(null, $path);
        $this->finder->get($data, $target);

        if ($target->found) {

            if (0 === strlen($target->childKey)) {
                $data = null;
            } elseif (is_array($target->parent)) {
                array_splice($target->parent, (int) $target->childKey, 1);
            } else {
                unset($target->parent->{$target->childKey});
            }
        }

        return $target->found;
    }

    public function getError()
    {
        return $this->error;
    }

    protected function init($data, $path)
    {
        $this->data = $data;
        $this->element =& $this->data;
        $this->error = '';

        return new Target($path);
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

    protected function getElement(Target &$target)
    {
        $this->element =& $this->finder->get($this->element, $target);

        if ($target->found) {

            if (is_array($target->parent)) {
                $target->setArray($target->childKey);
                $this->element =& $target->parent;
            }

            return true;
        }

        if ($this->jsonPatch) {
            $this->error = 'Path not found';
            return false;
        }

        return $this->buildElement($target);
    }

    protected function buildElement(Target &$target)
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
