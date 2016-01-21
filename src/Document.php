<?php

namespace JohnStevenson\JsonWorks;

use JohnStevenson\JsonWorks\Helpers\Builder;
use JohnStevenson\JsonWorks\Helpers\Finder;
use JohnStevenson\JsonWorks\Helpers\Tokenizer;

class Document extends BaseDocument
{
    /**
    * @var Builder
    */
    protected $builder;

    /**
    * @var Finder
    */
    protected $finder;

    /**
    * @var Tokenizer
    */
    protected $tokenizer;

    public function __construct()
    {
        parent::__construct();

        $this->builder = new Builder();
        $this->finder = new Finder();
        $this->tokenizer = new Tokenizer();
    }

    public function addValue($path, $value)
    {
        $this->lastError = null;
        $value = $this->formatter->copy($value);

        if (!$result = $this->builder->add($this->data, $path, $value)) {
            $this->lastError = $this->builder->getError();
        }

        return $result;
    }

    public function copyValue($fromPath, $toPath)
    {
        return $this->workMove($fromPath, $toPath, false);
    }

    public function deleteValue($path)
    {
        $tokens = $this->tokenizer->decode($path);

        $parent =& $this->finder->getParent($this->data, $tokens, $found, $lastKey);

        if ($found) {
            if (0 === strlen($lastKey)) {
                $this->data = null;
            } elseif (is_array($parent)) {
                array_splice($parent, (int) $lastKey, 1);
            } elseif (is_object($parent)) {
                unset($parent->$lastKey);
            }
        }

        return $found;
    }

    public function getValue($path, $default = null)
    {
        if (!$this->hasValue($path, $value)) {
            $value = $default;
        }

        return $value;
    }

    public function hasValue($path, &$value)
    {
        $tokens = $this->tokenizer->decode($path);
        $value = null;

        $element =& $this->finder->get($this->data, $tokens, $found);

        if ($found) {
            $value = $this->formatter->copy($element);
        }

        return $found;
    }

    public function moveValue($fromPath, $toPath)
    {
        return $this->workMove($fromPath, $toPath, true);
    }

    protected function workMove($fromPath, $toPath, $delete)
    {
        $result = false;

        if ($this->hasValue($fromPath, $value)) {
            if ($result = $this->addValue($toPath, $value)) {
                if ($delete) {
                    $this->deleteValue($fromPath);
                }
            }
        }

        return $result;
    }
}
