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

class MaxMinConstraint extends BaseConstraint implements ConstraintInterface
{
    protected bool $max;
    protected bool $length;
    protected string $caption;

    /**
     * @param mixed $data
     * @param stdClass|array<mixed> $schema
     */
    public function validate($data, $schema, ?string $key = null): void
    {
        if (!($schema instanceof stdClass)) {
            $error = Utils::getArgumentError('$schema', 'sdtClass', $schema);
            throw new \InvalidArgumentException($error);
        }

        if (!is_string($key)) {
            $error = Utils::getArgumentError('$key', 'string', $key);
            throw new \InvalidArgumentException($error);
        }

        if (!$this->getInteger($schema, $key, $value)) {
            return;
        }

        $this->setValues($data, $key);

        if ($this->length && is_string($data)) {
            $count = mb_strlen($data);
        } else {
            $count =  count((array) $data);
        }

        if (!$this->compare($count, $value)) {
            $this->setError($count, $value);
        }
    }

    /**
    * Sets protected values
    *
    * @param mixed $data
    */
    protected function setValues($data, string $key): void
    {
        $this->max = Utils::isMatch('/^max/', $key);

        if ($this->length = Utils::isMatch('/Length$/', $key)) {
            $this->caption = 'characters';
        } else {
            $this->caption = is_object($data) ? 'properties' : 'elements';
        }
    }

    /**
    * Returns true if a valid integer is found in the schema
    *
    * @param int $value Set by method
    * @throws \RuntimeException
    */
    protected function getInteger(stdClass $schema, string $key, &$value): bool
    {
        if (!$this->getValue($schema, $key, $value, ['integer'])) {
            return false;
        }

        if ($value >= 0) {
            return true;
        }

        $error = $this->formatError('>= 0', (string) $value);
        throw new \RuntimeException($error);
    }

    protected function compare(int $count, int $value): bool
    {
        return $this->max ? $count <= $value : $count >= $value;
    }

    protected function setError(int $count, int $value): void
    {
        if ($this->max) {
            $error = "has too many %s [%d], maximum is '%d'";
        } else {
            $error = "has too few %s [%d], minimum is '%d'";
        }

        $this->addError(sprintf($error, $this->caption, $count, $value));
    }
}
