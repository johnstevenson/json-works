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

class StringConstraint extends BaseConstraint implements ConstraintInterface
{
    protected MaxMinConstraint $maxMin;
    protected FormatChecker $formatChecker;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->maxMin = new MaxMinConstraint($manager);
        $this->formatChecker = new FormatChecker($manager);
    }

    /**
     * @param mixed $data
     * @param stdClass|array<mixed> $schema
     */
    public function validate($data, $schema, ?string $key = null): void
    {
        if (!is_string($data)) {
            $error = Utils::getArgumentError('$data', 'string', $data);
            throw new \InvalidArgumentException($error);
        }

        if (!($schema instanceof stdClass)) {
            $error = Utils::getArgumentError('$schema', 'sdtClass', $schema);
            throw new \InvalidArgumentException($error);
        }

        // maxLength
        $this->maxMin->validate($data, $schema, 'maxLength');

        // minLength
        $this->maxMin->validate($data, $schema, 'minLength');

        // format
        if ($this->getString($schema, 'format', $format)) {
            $this->formatChecker->check($data, $format);
        }

        // pattern
        if ($this->getString($schema, 'pattern', $pattern)) {
            $this->checkPattern($data, $pattern);
        }
    }

    protected function checkPattern(string $data, string $pattern): void
    {
        if (!$this->matchPattern($pattern, $data)) {
            $this->addError(sprintf('does not match pattern: %s', $pattern));
        }
    }

    /**
    * @param mixed $value Set by method
    */
    protected function getString(stdClass $schema, string $key, &$value): bool
    {
        return $this->getValue($schema, $key, $value, ['string']);
    }
}
