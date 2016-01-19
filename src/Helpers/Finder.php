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

/**
* A class for finding value using JSON Pointers
*/
class Finder
{
    protected $element;

    public function &get(&$data, &$tokens, &$found)
    {
        $this->element =& $data;

        while (!empty($tokens)) {
            $found = false;
            $type = gettype($this->element);
            $key = $tokens[0];

            if ('object' === $type) {
                $found = $this->findObject($key);
            } elseif ('array' === $type) {
                $found = $this->findArray($key);
            }

            if (!$found) {
                break;
            }

            array_shift($tokens);
        }

        return $this->element;
    }

    protected function findArray($key)
    {
        if ($result = $this->isArrayKey($key, $index)) {
            if ($result = array_key_exists($index, $this->element)) {
                $this->element = &$this->element[$index];
            }
        }

        return $result;
    }

    protected function findObject($key)
    {
        if ($result = property_exists($this->element, $key)) {
            $this->element = &$this->element->$key;
        }

        return $result;
    }

    protected function isArrayKey($value, &$index)
    {
        $index = null;

        if (preg_match('/^0*\d+$/', $value)) {
            $index = (int) $value;
        }

        return $index !== null;
    }
}
