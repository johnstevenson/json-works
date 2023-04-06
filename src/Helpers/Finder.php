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

use JohnStevenson\JsonWorks\Helpers\Error;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;

/**
* A class for finding a value using JSON Pointers
*/
class Finder
{
    /** @var mixed */
    protected $element;
    protected Target $target;

    /**
    * Returns true if an element is found
    *
    * @api
    * @param mixed $data
    * @param mixed $element Set by method if found
    * @param string $error Set by method
    */
    public function find(string $path, $data, &$element, &$error): bool
    {
        $target = new Target($path, $error);

        if ($result = $this->get($data, $target)) {
            $element = $target->element;
        }

        return $result;
    }

    /**
    * Returns true if an element is found
    *
    * @api
    * @param mixed $data
    */
    public function get(&$data, Target $target): bool
    {
        $this->element =& $data;
        $this->target = $target;

        if ($target->invalid) {
            return false;
        }

        $found = Utils::arrayIsEmpty($target->tokens);

        if (!$found) {
            $found = $this->search($target->tokens);
        }

        $target->setResult($found, $this->element);

        return $found;
    }

    /**
    * Returns true if the element is found
    *
    * @param array<string> $tokens Modified by method
    */
    protected function search(array &$tokens): bool
    {
        // tokens is guaranteed not empty
        while (Utils::arrayNotEmpty($tokens)) {
            $token = $tokens[0];

            if (count($tokens) === 1) {
                $this->target->parent =& $this->element;
                $this->target->childKey = $token;
            }

            if (!$this->findContainer($token)) {
                return false;
            }

            $token = array_shift($tokens);
            if ($token === null) {
                break;
            }

            $this->target->setFoundPath($token);
        }

        return true;
    }

    /**
    * Returns true if a token is found at the current data root
    *
    * A reference to the value is placed in $this->element
    */
    protected function findContainer(string $token): bool
    {
        $found = false;

        if (is_object($this->element)) {
            $found = $this->findObject($token);
        } elseif (is_array($this->element)) {

            if ($token !== '-') {
                $found = $this->findArray($token);
            }
        }

        return $found;
    }

    /**
    * Returns true if the token is an existing array key
    *
    * Sets $this->element to reference the value
     */
    protected function findArray(string $token): bool
    {

        if (!$this->isArrayKey($token, $index)) {
            $this->target->setError(Error::ERR_PATH_KEY);
            return false;
        }

        if ($result = (isset($this->element[$index]) || array_key_exists($index, $this->element))) {
            $this->element = &$this->element[$index];
        }

        return $result;
    }

    /**
    * Returns true if the token is an existing object property key
    *
    * Sets $this->element to reference the value
    */
    protected function findObject(string $token): bool
    {
        if ($result = property_exists($this->element, $token)) {
            // @phpstan-ignore-next-line
            $this->element = &$this->element->$token;
        }

        return $result;
    }

    /**
    * Returns true if the token is a valid array key
    *
    * @api
    * @param mixed $index Set to an integer on success
    */
    protected function isArrayKey(string $token, &$index): bool
    {
        $result = Utils::isMatch('/^((0)|([1-9]\d*))$/', $token);

        if ($result) {
            $index = (int) $token;
        }

        return $result;
    }
}
