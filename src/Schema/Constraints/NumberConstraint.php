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
        $this->checkMaxMin($data, $schema, 'maximum', true);


        // minimum
        $this->checkMaxMin($data, $schema, 'minimum', false);
    }

    protected function checkMaxMin($data, $schema, $key, $max)
    {
        $exclusiveKey = sprintf('exclusive%s', ucfirst($key));
        $exclusive = $this->getExclusive($schema, $exclusiveKey);

        if ($this->getNumber($schema, $key, false, $value)) {
            $this->compare($data, $value, $exclusive, $max);

        } elseif ($exclusive) {
            $error = $this->getSchemaError($key, '');
            throw new \RuntimeException($error);
        }
    }

    protected function getExclusive($schema, $key)
    {
        return $this->getValue($schema, $key, $value, $type, 'boolean');
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

    protected function compare($data, $value, $exclusive, $max)
    {
        if (!$this->precisionCompare($data, $value, $exclusive, $max)) {
            $this->setError($value, $exclusive, $max);
        }
    }

    protected function precisionCompare($data, $value, $exclusive, $max)
    {
        $comp = bccomp(strval($data), strval($value), 16);

        if ($max) {
            $result = $exclusive ? $comp < 0 : $comp <= 0;
        } else {
            $result = $exclusive ? $comp > 0 : $comp >= 0;
        }

        return $result;
    }

    protected function setError($value, $exclusive, $max)
    {
        $caption = $max ? 'less' : 'greater';
        $equals = $exclusive ? 'or equal to ' : '';
        $error = sprintf("value must be %s than %s'%f'", $caption, $equals, $value);

        $this->addError($error);
    }
}
