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
use JohnStevenson\JsonWorks\Schema\Constraint\Manager;
use JohnStevenson\JsonWorks\Schema\Constraint\MaxMinConstraint;
use JohnStevenson\JsonWorks\Schema\Constraint\PropertiesConstraint;

class ObjectConstraint extends BaseConstraint implements ConstraintInterface
{
    protected MaxMinConstraint $maxMin;
    protected PropertiesConstraint $properties;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->maxMin = new MaxMinConstraint($manager);
        $this->properties = new PropertiesConstraint($manager);
    }

    /**
     * @param mixed $data
     * @param stdClass|array<mixed> $schema
     */
    public function validate($data, $schema, ?string $key = null): void
    {
        if (!is_object($data)) {
            $error = Utils::getArgumentError('$data', 'object', $data);
            throw new \InvalidArgumentException($error);
        }

        if (!($schema instanceof stdClass)) {
            $error = Utils::getArgumentError('$schema', 'sdtClass', $schema);
            throw new \InvalidArgumentException($error);
        }

        // max and min
        $this->checkMaxMin($data, $schema);

        // required
        $this->checkRequired($data, $schema);

        $this->properties->validate($data, $schema);
    }

    protected function checkMaxMin(object $data, stdClass $schema): void
    {
        // maxProperties
        $this->maxMin->validate($data, $schema, 'maxProperties');

        // minProperties
        $this->maxMin->validate($data, $schema, 'minProperties');
    }

    protected function checkRequired(object $data, stdClass $schema): void
    {
        if (!$this->getValue($schema, 'required', $value, ['array'])) {
            return;
        }

        $this->manager->dataChecker->checkArray($value, 'required');

        foreach ($value as $name) {
            if (!property_exists($data, $name)) {
                $this->addError(sprintf("is missing required property '%s'", $name));
            }
        }
    }
}
