<?php declare(strict_types=1);

/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks;

use JohnStevenson\JsonWorks\Helpers\Patcher;
use JohnStevenson\JsonWorks\Helpers\Finder;


/**
 * A class for Querying and manipulating json data. *
 * @api
 */
class Document extends BaseDocument
{
    private Finder $finder;

    public function __construct()
    {
        parent::__construct();
        $this->finder = new Finder();
    }

    /**
    * Adds an element to the data
    *
    * @param string|array<string> $path
    * @param mixed $value
    */
    public function addValue($path, $value): bool
    {
        $this->error = '';
        $path = $this->getPath($path, '$path');
        $value = $this->formatter->copy($value);
        $patcher = new Patcher();

        if (!$result = $patcher->add($this->data, $path, $value)) {
            $this->error = $patcher->getError();
        }

        return $result;
    }

    /**
     * @param string|array<string> $fromPath
     * @param string|array<string> $toPath
     */
    public function copyValue($fromPath, $toPath): bool
    {
        $fromPath = $this->getPath($fromPath, '$fromPath');
        $toPath = $this->getPath($toPath, '$toPath');

        return $this->doMove($fromPath, $toPath, false);
    }

    /**
     * @param string|array<string> $path
      */
    public function deleteValue($path): bool
    {
        $path = $this->getPath($path, '$path');
        $patcher = new Patcher();

        if (!$result = $patcher->remove($this->data, $path)) {
            $this->error = $patcher->getError();
        }

        return $result;
    }

    /**
     * @param string|array<string> $path
     * @param mixed $default
     * @return mixed
     */
    public function getValue($path, $default = null)
    {
        $path = $this->getPath($path, '$path');

        if (!$this->hasValue($path, $value)) {
            $value = $default;
        }

        $this->error = '';

        return $value;
    }

    /**
     * @param string|array<string> $path
     * @param mixed $value
     */
    public function hasValue($path, &$value): bool
    {
        $path = $this->getPath($path, '$path');
        $value = null;
        $this->error = $error = '';

        if ($result = $this->finder->find($path, $this->data, $element, $error)) {
            $value = $this->formatter->copy($element);
        } else {
            $this->error = $error;
        }

        return $result;
    }

    /**
     * @param string|array<string> $fromPath
     * @param string|array<string> $toPath
     */
    public function moveValue($fromPath, $toPath): bool
    {
        $fromPath = $this->getPath($fromPath, '$fromPath');
        $toPath = $this->getPath($toPath, '$toPath');

        return $this->doMove($fromPath, $toPath, true);
    }

    private function doMove(string $fromPath, string $toPath, bool $delete): bool
    {
        $result = false;
        $this->error = '';

        if ($this->hasValue($fromPath, $value)) {
            if ($result = $this->addValue($toPath, $value)) {
                if ($delete) {
                    $this->deleteValue($fromPath);
                }
            }
        }

        return $result;
    }

    /**
     * @param string|array<string> $value
     */
    private function getPath($value, string $varName): string
    {
        if (is_string($value)) {
            return $value;
        }

        $tokenizer = new Tokenizer();
        return $tokenizer->encode($value);
    }
}
