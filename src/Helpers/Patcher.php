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

    public function __construct()
    {
        $this->builder = new Builder();
        $this->finder = new Finder();
    }

    public function add(&$data, $path, $value)
    {
        $target = $this->init($data);

        if (!$this->findElement($path, $target)) {
            return false;
        }

        if ($result = $this->addToData($value, $target)) {
            $data = $this->data;
        }

        return $result;
    }

    public function remove(&$data, $path)
    {
        $target = $this->init(null);
        $this->finder->get($path, $data, $target);

        if ($target->found) {

            if (0 === strlen($target->lastKey)) {
                $data = null;
            } elseif (is_array($target->parent)) {
                array_splice($target->parent, (int) $target->lastKey, 1);
            } else {
                unset($target->parent->{$target->lastKey});
            }
        }

        return $target->found;
    }

    public function getError()
    {
        return $this->error;
    }

    protected function init($data = null)
    {
        $this->data = $data;
        $this->element =& $this->data;
        $this->error = '';

        return new Target();
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
        if ($target->type === Target::TYPE_PUSH) {
            array_push($this->element, $value);
        } elseif ($target->type === Target::TYPE_PROPKEY) {
            $this->element->{$target->propKey} = $value;
        } elseif ($this->data === $this->element) {
            return $this->addToRoot($value);
        } else {
            $this->element = $value;
        }

        return true;
    }

    protected function findElement($path, Target &$target)
    {
        $this->element =& $this->finder->get($path, $this->element, $target);

        if ($target->found) {
            return true;
        }

        try {
            $this->element =& $this->builder->add($this->element, $target);
            $result = true;
        } catch (InvalidArgumentException $e) {
            $this->error = $e->getMessage();
            $result = false;
        }

        return $result;
    }
}
