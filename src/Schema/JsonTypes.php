<?php
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
    * @return string
    */
    public function getGeneric($value)
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
    * @return string
    */
    public function getSpecific($value)
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
    * @param string $type
    * @return bool
    */
    public function checkType($value, $type)
    {
        if (in_array($type, ['integer', 'number'])) {
            $method = 'is' . ucfirst($type);
            return $this->$method($value);
        }

        return $type === $this->getGeneric($value);
    }

    /**
    * Returns true if array values are the same type
    *
    * @param array $data
    * @param string $type
    * @return bool
    */
    public function arrayOfType(array $data, $type)
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
    * @return bool
    */
    protected function isInteger($value)
    {
        return is_integer($value) || (is_float($value) && $value === floor($value));
    }

    /**
    * Returns true if a value is a json number
    *
    * @param mixed $value
    * @return bool
    */
    protected function isNumber($value)
    {
        return is_float($value) || is_integer($value);
    }
}
