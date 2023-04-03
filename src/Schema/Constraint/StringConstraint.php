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

class StringConstraint extends BaseConstraint
{
    protected MaxMinConstraint $maxMin;
    protected FormatConstraint $format;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->maxMin = new MaxMinConstraint($manager);
        $this->format = new FormatConstraint($manager);
    }

    /**
    * @param mixed $data
    */
    public function validate($data, stdClass $schema): void
    {
        // maxLength
        $this->maxMin->validate($data, $schema, 'maxLength');

        // minLength
        $this->maxMin->validate($data, $schema, 'minLength');

        // format
        if ($this->getString($schema, 'format', $format)) {
            $this->format->validate($data, $format);
        }

        // pattern
        if ($this->getString($schema, 'pattern', $pattern)) {
            $this->checkPattern($data, $pattern);
        }
    }

    /**
    * @param mixed $data
    */
    protected function checkPattern($data, string $pattern): void
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
        return $this->getValue($schema, $key, $value, 'string');
    }
}
