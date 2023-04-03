<?php declare(strict_types=1);

namespace JohnStevenson\JsonWorks;

use JohnStevenson\JsonWorks\Helpers\Patcher;
use JohnStevenson\JsonWorks\Helpers\Finder;

class Document extends BaseDocument
{
    protected Finder $finder;

    public function __construct()
    {
        parent::__construct();
        $this->finder = new Finder();
    }

    /**
    * Adds an element to the data
    *
    * @param string $path
    * @param mixed $value
    * @return bool If the value was added
    */
    public function addValue(string $path, $value): bool
    {
        $this->lastError = '';
        $value = $this->formatter->copy($value);
        $patcher = new Patcher();

        if (!$result = $patcher->add($this->data, $path, $value)) {
            $this->lastError = $patcher->getError();
        }

        return $result;
    }

    public function copyValue(string $fromPath, string $toPath): bool
    {
        return $this->workMove($fromPath, $toPath, false);
    }

    public function deleteValue(string $path): bool
    {
        $patcher = new Patcher();

        return $patcher->remove($this->data, $path);
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function getValue(string $path, $default = null)
    {
        if (!$this->hasValue($path, $value)) {
            $value = $default;
        }

        return $value;
    }

    /**
     * @param mixed $value
     */
    public function hasValue(string $path, &$value): bool
    {
        $value = null;

        if ($result = $this->finder->find($path, $this->data, $element, $this->lastError)) {
            $value = $this->formatter->copy($element);
        }

        return $result;
    }

    public function moveValue(string $fromPath, string $toPath): bool
    {
        return $this->workMove($fromPath, $toPath, true);
    }

    protected function workMove(string $fromPath, string $toPath, bool $delete): bool
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
