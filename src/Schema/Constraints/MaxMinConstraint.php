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

class MaxMinConstraint extends BaseConstraint
{
    /**
    * @var bool
    */
    protected $max;

    /**
    * @var string
    */
    protected $caption;

    /**
    * The main method
    *
    * @param mixed $data
    * @param mixed $schema
    * @param mixed $key
    */
    protected function run($data, $schema, $key = null)
    {
        if (!$this->getInteger($schema, $key, $value)) {
            return;
        }

        $this->setValues($data, $key);
        $count = count((array) $data);

        if (!$this->compare($count, $value)) {
            $this->setError($value);
        }
    }

    /**
    * Sets protected values
    *
    * @param mixed $data
    * @param string $key
    */
    protected function setValues($data, $key)
    {
        $this->max = (bool) preg_match('/^max/', $key);
        $this->caption = is_object($data) ? 'properties' : 'items';
    }

    /**
    * Fetches a value from the schema and checks that it is a valid integer
    *
    * @param mixed $schema
    * @param string $key
    * @param mixed $value
    */
    protected function getInteger($schema, $key, &$value)
    {
        if ($result = $this->getValue($schema, $key, $value, $type)) {
            $this->checkInteger($value, $type);
        }

        return $result;
    }

    /**
    * Checks that the value is a positive integer
    *
    * @param mixed $value
    * @param string $type
    */
    protected function checkInteger($value, $type)
    {
        $error = '';

        if ($type !== 'integer') {
            $error = 'integer';
        } elseif ($value < 0) {
            $error = '>= 0';
        }

        if ($error) {
            $this->throwSchemaError($error, $value);
        }
    }

    /**
    * The main comparison
    *
    * @param integer $count
    * @param integer $value
    */
    protected function compare($count, $value)
    {
        return $this->max ? $count <= $value : $count >= $value;
    }

    /**
    * Adds an appropriate error
    *
    * @param integer $value
    */
    protected function setError($value)
    {
        if ($this->max) {
            $error = "has too many %d, maximum '%d'";
        } else {
            $error = "has too few %d, minimum '%d'";
        }

        $this->addError(sprintf($error, $this->caption, $value));
    }
}
