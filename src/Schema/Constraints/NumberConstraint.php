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

class NumberConstraint extends BaseConstraint
{
    public function validate($data, $schema)
    {
        // maximum
        $this->checkMaxMin($data, $schema, 'maximum');


        // minimum
        $this->checkMaxMin($data, $schema, 'minimum');
    }

    protected function checkMaxMin($data, $schema, $key)
    {
        $exclusiveKey = sprintf('exclusive%s', ucfirst($key));
        $method = sprintf('compare%s', ucfirst($key));
        $exclusive = $this->getExclusive($schema, $exclusiveKey);

        if ($this->getNumber($schema, $key, false, $value)) {
            $this->$method($data, $value, $exclusive);
        } elseif ($exclusive) {
            $error = $this->getSchemaError($key, '');
            throw new \RuntimeException($error);
        }
    }

    protected function getNumber($schema, $key, $positiveNonZero, &$value)
    {
        if (!$this->getValue($schema, $key, $value, $type, ['double', 'integer'])) {
            return false;
        }

        if (!$positiveNonZero || $value > 0) {
            return true;
        }

        $error = $this->getSchemaError('> 0', $value);
        throw new \RuntimeException($error);
    }

    protected function getExclusive($schema, $key)
    {
        return $this->getValue($schema, $key, $value, $type, 'boolean');
    }

    protected function compareMaximum($data, $maximum, $exclusive)
    {
        if ($exclusive) {
            $result = $data < $maximum;
        } else {
            $result = $this->precisionCompare($data, $maximum) <= 0;
        }

        if (!$result) {
            $this->setError($maximum, true, $exclusive);
        }
    }

    protected function compareMinimum($data, $minimum, $exclusive)
    {
        if ($exclusive) {
            $result = $data > $minimum;
        } else {
            $result = $this->precisionCompare($data, $minimum) >= 0;
        }

        if (!$result) {
            $this->setError($minimum, false, $exclusive);
        }
    }

    protected function precisionCompare($value1, $value2)
    {
        return bccomp(strval($value1), strval($value2), 16);
    }

    protected function setError($value, $max, $exclusive)
    {
        $caption = $max ? 'less' : 'greater';
        $equals = $exclusive ? 'or equal to ' : '';
        $error = sprintf("value must be %s than %s'%f'", $caption, $equals, $value);

        $this->addError($error);
    }
}
