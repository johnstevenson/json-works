<?php

namespace JohnStevenson\JsonWorks;

use JohnStevenson\JsonWorks\Helpers\Patcher;
use JohnStevenson\JsonWorks\Helpers\Finder;

class Document extends BaseDocument
{
    /**
    * @var Helpers\Patcher
    */
    protected $patcher;

    /**
    * @var Helpers\Finder
    */
    protected $finder;

    public function __construct()
    {
        parent::__construct();
        $this->patcher = new Patcher();
        $this->finder = new Finder();
    }

    public function addValue($path, $value)
    {
        $this->lastError = null;
        $value = $this->formatter->copy($value);

        if (!$result = $this->patcher->add($this->data, $path, $value)) {
            $this->lastError = $this->patcher->getError();
        }

        return $result;
    }

    public function copyValue($fromPath, $toPath)
    {
        return $this->workMove($fromPath, $toPath, false);
    }

    public function deleteValue($path)
    {
        return $this->patcher->remove($this->data, $path);
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
        $value = null;

        if ($result = $this->finder->find($path, $this->data, $element)) {
            $value = $this->formatter->copy($element);
        }

        return $result;
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
