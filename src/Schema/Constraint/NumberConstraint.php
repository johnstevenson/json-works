<?php declare(strict_types=1);

/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Schema\Constraint;

use \stdClass;

use JohnStevenson\JsonWorks\Helpers\Utils;

class NumberConstraint extends BaseConstraint implements ConstraintInterface
{
    /**
     * @param mixed $data
     * @param stdClass|array<mixed> $schema
     */
    public function validate($data, $schema, ?string $key = null): void
    {
        if (!is_int($data) && !is_float($data)) {
            $error = Utils::getArgumentError('$data', 'int|float', $data);
            throw new \InvalidArgumentException($error);
        }

        if (!($schema instanceof stdClass)) {
            $error = Utils::getArgumentError('$schema', 'sdtClass', $schema);
            throw new \InvalidArgumentException($error);
        }

        // maximum
        $this->checkMaxMin($data, $schema, 'maximum', true);

        // minimum
        $this->checkMaxMin($data, $schema, 'minimum', false);

        // multipleOf
        $this->checkMultipleOf($data, $schema);
    }

    /**
     * @param int|float $data
     */
    protected function checkMaxMin($data, stdClass $schema, string $key, bool $max): void
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

    /**
     * @param int|float $data
     */
    protected function checkMultipleOf($data, stdClass $schema): void
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

    protected function getExclusive(stdClass $schema, string $key): bool
    {
        return $this->getValue($schema, $key, $value, ['boolean']);
    }

    /**
     * @param int|float $value Set by method
     */
    protected function getNumber(stdClass $schema, string $key, bool $positiveNonZero, &$value): bool
    {
        if (!$this->getValue($schema, $key, $value, ['number', 'integer'])) {
            return false;
        }

        if (!$positiveNonZero || $value > 0) {
            return true;
        }

        $error = $this->formatError('> 0', (string) $value);
        throw new \RuntimeException($error);
    }

    /**
     * @param int|float $data
     * @param int|float $value
     */
    protected function compare($data, $value, bool $exclusive, bool $max): void
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

    /**
     * @param int|double $data
     * @param int|float $value
     */
    protected function precisionCompare($data, $value, bool $exclusive, bool $max): bool
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
