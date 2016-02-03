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
    protected $max;
    protected $caption;

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

    protected function setValues($data, $key)
    {
        $this->max = preg_match('/^max/', $key);
        $this->caption = is_object($data) ? 'properties' : 'items';
    }

    protected function getInteger($schema, $key, &$value)
    {
        if ($result = $this->getValue($schema, $key, $value, $type)) {
            $this->checkInteger($value, $type);
        }

        return $result;
    }

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

    protected function compare($count, $value)
    {
        return $this->max ? $count <= $value : $count >= $value;
    }

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
