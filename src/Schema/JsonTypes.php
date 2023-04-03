<?php declare(strict_types=1);
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Schema;

class JsonTypes
{
    /**
    * Returns the variable type
    *
    * Numeric variables are reported as numbers
    *
    * @param mixed $value
    */
    public function getGeneric($value): string
    {
        $result = $this->getSpecific($value);

        return $result === 'integer' ?  'number' : $result;
    }

    /**
    * Returns the variable type
    *
    * Numeric variables are reported as either numbers or integers
    *
    * @param mixed $value
    */
    public function getSpecific($value): string
    {
        $result = strtolower(gettype($value));

        if ($result === 'double') {
            $result = $this->isInteger($value) ? 'integer' : 'number';
        }

        return $result;
    }

    /**
    * Returns true if a value is the generic type
    *
    * @param mixed $value
    */
    public function checkType($value, string $type): bool
    {
        if ($type === 'integer') {
            return $this->isInteger($value);
        }

        if ($type === 'number') {
            return $this->isNumber($value);
        }

        return $type === $this->getGeneric($value);
    }

    /**
    * Returns true if array values are the same type
    *
    * @param array<mixed> $data
    */
    public function arrayOfType(array $data, string $type): bool
    {
        foreach ($data as $value) {

            if (!$this->checkType($value, $type)) {
                return false;
            }
        }

        return true;
    }

    /**
    * Returns true if a value is an integer
    *
    * Large integers may be stored as a float (Issue:1). Note that the data
    * may have been truncated to fit a 64-bit PHP_MAX_INT
    *
    * @param mixed $value
    */
    protected function isInteger($value): bool
    {
        return is_integer($value) || (is_float($value) && abs($value) >= PHP_INT_MAX);
    }

    /**
    * Returns true if a value is a json number
    *
    * @param mixed $value
    */
    protected function isNumber($value): bool
    {
        return is_float($value) || is_integer($value);
    }
}
