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

use JohnStevenson\JsonWorks\Helpers\Finder;
use JohnStevenson\JsonWorks\Helpers\Patch\Builder;
use JohnStevenson\JsonWorks\Helpers\Patch\BuildException;
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
    * @var Patch\Target
    */
    protected $target;

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
        $this->initAdd($data);

        if (!$this->findElement($path)) {
            return false;
        }

        if ($result = $this->addToData($value)) {
            $data = $this->data;
        }

        return $result;
    }

    public function remove(&$data, $path)
    {
        $parent =& $this->finder->getParent($path, $data, $found, $lastKey);

        if ($found) {

            if (0 === strlen($lastKey)) {
                $data = null;
            } elseif (is_array($parent)) {
                array_splice($parent, (int) $lastKey, 1);
            } else {
                unset($parent->$lastKey);
            }
        }

        return $found;
    }

    public function getError()
    {
        return $this->error;
    }

    protected function initAdd($data)
    {
        $this->data = $data;
        $this->element =& $this->data;
        $this->target = new Target();
        $this->error = '';
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

    protected function addToData($value)
    {
        if ($this->target->type === Target::TYPE_PUSH) {
            array_push($this->element, $value);
        } elseif ($this->target->type === Target::TYPE_PROPKEY) {
            $this->element->{$this->target->propKey} = $value;
        } elseif ($this->data === $this->element) {
            return $this->addToRoot($value);
        } else {
            $this->element = $value;
        }

        return true;
    }

    protected function findElement($path)
    {
        $this->element =& $this->finder->get($path, $this->element, $tokens, $found);

        if ($found) {
            return true;
        }

        $result = true;

        try {
            $this->element =& $this->builder->add($tokens, $this->element, $this->target);
        } catch (BuildException $e) {
            $this->error = $e->getMessage();
            $result = false;
        }

        return $result;
    }
}
