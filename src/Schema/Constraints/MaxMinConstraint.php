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
    * @var bool
    */
    protected $length;

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
    public function validate($data, $schema, $key)
    {
        if (!$this->getInteger($schema, $key, $value)) {
            return;
        }

        $this->setValues($data, $key);
        $count = $this->length ? mb_strlen($data) : count((array) $data);

        if (!$this->compare($count, $value)) {
            $this->setError($count, $value);
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

        if ($this->length = (bool) preg_match('/Length$/', $key)) {
            $this->caption = 'characters';
        } else {
            $this->caption = is_object($data) ? 'properties' : 'elements';
        }
    }

    /**
    * Returns true if a valid integer is found in the schema
    *
    * @param mixed $schema
    * @param string $key
    * @param mixed $value Set by method
    * @return bool
    * @throws RuntimeException
    */
    protected function getInteger($schema, $key, &$value)
    {
        if (!$this->getValue($schema, $key, $value, 'integer')) {
            return false;
        }

        if ($value >= 0) {
            return true;
        }

        $error = $this->formatError('>= 0', $value);
        throw new \RuntimeException($error);
    }

    /**
    * The main comparison
    *
    * @param integer $count
    * @param integer $value
    * @return bool
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
    protected function setError($count, $value)
    {
        if ($this->max) {
            $error = "has too many %s [%d], maximum is '%d'";
        } else {
            $error = "has too few %s [%d], minimum is '%d'";
        }

        $this->addError(sprintf($error, $this->caption, $count, $value));
    }
}
