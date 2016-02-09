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

        // multipleOf
        $this->checkMultipleOf($data, $schema);
    }

    protected function checkMaxMin($data, $schema, $key, $max)
    {
        $exclusiveKey = sprintf('exclusive%s', ucfirst($key));
        $exclusive = $this->getExclusive($schema, $exclusiveKey);

        if ($this->getNumber($schema, $key, false, $value)) {
            $this->compare($data, $value, $exclusive, $max);

        } elseif ($exclusive) {
            $error = $this->formatError($key, '');
            throw new \RuntimeException($error);
        }
    }

    protected function checkMultipleOf($data, $schema)
    {
        if (!$this->getNumber($schema, 'multipleOf', true, $multipleOf)) {
            return;
        }

        $quotient = bcdiv(strval($data), strval($multipleOf), 16);
        $intqt = (int) $quotient;

        if (bccomp(strval($quotient), strval($intqt), 16) !== 0) {
            $error = sprintf("value must be a multiple of '%f'", $multipleOf);
            $this->addError($error);
        }
    }

    protected function getExclusive($schema, $key)
    {
        return $this->getValue($schema, $key, $value, 'boolean');
    }

    protected function getNumber($schema, $key, $positiveNonZero, &$value)
    {
        if (!$this->getValue($schema, $key, $value, ['number', 'integer'])) {
            return false;
        }

        if (!$positiveNonZero || $value > 0) {
            return true;
        }

        $error = $this->formatError('> 0', $value);
        throw new \RuntimeException($error);
    }

    protected function compare($data, $value, $exclusive, $max)
    {
        if ($this->precisionCompare($data, $value, $exclusive, $max)) {
            return;
        }

        // format the error
        $caption = $max ? 'less' : 'greater';
        $equals = $exclusive ? 'or equal to ' : '';
        $error = sprintf("value must be %s than %s'%f'", $caption, $equals, $value);

        $this->addError($error);
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
}
