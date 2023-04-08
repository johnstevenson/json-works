<?php declare(strict_types=1);

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
use JohnStevenson\JsonWorks\Helpers\Utils;

/**
* A class for building json
*/
class Patcher
{
    protected string $error = '';
    protected bool $jsonPatch;
    protected Builder $builder;
    protected Finder $finder;

    /**
    * Constructor
    *
    * If jsonPatch is set, elements will only be added to the root or an
    * existing element. See RFC 6902 (http://tools.ietf.org/html/rfc6902)
    */
    public function __construct(bool $jsonPatch = false)
    {
        $this->jsonPatch = $jsonPatch;
        $this->builder = new Builder();
        $this->finder = new Finder();
    }

    /**
    * Adds an element to the data
    *
    * @param mixed $data
    * @param mixed $value
    */
    public function add(&$data, string $path, $value): bool
    {
        if ($result = $this->getElement($data, $path, $value, $target)) {
            $result = $this->addData($target, $value);
        }

        return $result;
    }

    /**
    * Removes an element if found
    *
    * @param mixed $data
    */
    public function remove(&$data, string $path): bool
    {
        if ($result = $this->find($data, $path, $target)) {

            if (0 === strlen($target->childKey)) {
                $data = null;
            } elseif (is_array($target->parent)) {
                array_splice($target->parent, (int) $target->childKey, 1);
            } else {
                // @phpstan-ignore-next-line
                unset($target->parent->{$target->childKey});
            }
        }

        return $result;
    }

    /**
    * Replaces an element if found
    *
    * @param mixed $data
    * @param mixed $value
    */
    public function replace(&$data, string $path, $value): bool
    {
        if ($result = $this->find($data, $path, $target)) {
            $result = $this->addData($target, $value);
        }

        return $result;
    }

    public function getError(): string
    {
        return $this->error;
    }

    /**
    * Adds or modifies the data
    *
    * @param mixed $value
    */
    protected function addData(Target $target, $value): bool
    {
        $result = true;
        $error = '';

        switch ($target->type) {
            case Target::TYPE_VALUE:
                $target->element = $value;
                break;
            case Target::TYPE_OBJECT:
                if (is_object($target->element)) {
                    // @phpstan-ignore-next-line
                    $target->element->{$target->key} = $value;
                } else {
                    $error = sprintf("property '%s'", $target->key);
                }
                break;
            case Target::TYPE_ARRAY:
                if (is_array($target->element)) {
                    // @phpstan-ignore-next-line
                    array_splice($target->element, $target->key, 0, [$value]);
                } else {
                    $error =sprintf("offset '%d'", $target->key);
                }
                break;
        }

        if (Utils::stringNotEmpty($error)) {
            $result = false;
            $type = gettype($target->element);
            $this->error = sprintf('Cannot assign %s to %s', $error, $type);
        }

        return $result;
    }

    /**
    * Returns true if we can create a new element
    */
    protected function canBuild(Target $target): bool
    {
        return !$target->invalid && !($this->jsonPatch && Utils::arrayNotEmpty($target->tokens));
    }

    /**
    * Returns true if an element is found
    *
    * @param mixed $data
    */
    protected function find(&$data, string $path, ?Target &$target): bool
    {
        $target = new Target($path, $this->error);

        return $this->finder->get($data, $target);
    }

    /**
    * Returns true if an element is found or built
    *
    * @param mixed $data
    * @param mixed $value
    * @param Target $target Set by method
    * @return bool
    */
    protected function getElement(&$data, string $path, &$value, &$target): bool
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
    * @param mixed $value
    * @return bool
    */
    protected function buildElement(Target $target, &$value): bool
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
