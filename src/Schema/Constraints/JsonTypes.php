<?php
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Schema\Constraints;

class JsonTypes
{
    public function getGeneric($value)
    {
        $result = strtolower(gettype($value));

        if (in_array($result, ['double', 'integer'])) {
            $result = 'number';
        }

        return $result;
    }

    public function checkType($value, $type)
    {
        if (in_array($type, ['integer', 'number'])) {
            $method = 'is' . ucfirst($type);
            return $this->$method($value);
        }

        return $type === $this->getGeneric($value);
    }

    protected function isInteger($value)
    {
        // Large integers may be stored as a float (Issue:1). Note that data
        // may have been truncated to fit a 64-bit PHP_MAX_INT
        return is_integer($value) || (is_float($value) && $value === floor($value));
    }

    protected function isNumber($value)
    {
        return is_float($value) || is_integer($value);
    }
}
