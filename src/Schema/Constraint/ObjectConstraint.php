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

use JohnStevenson\JsonWorks\Schema\Constraint\Manager;
use JohnStevenson\JsonWorks\Schema\Constraint\MaxMinConstraint;
use JohnStevenson\JsonWorks\Schema\Constraint\PropertiesConstraint;

class ObjectConstraint extends BaseConstraint
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
     */
    public function validate($data, stdClass $schema): void
    {
        // max and min
        $this->checkMaxMin($data, $schema);

        // required
        $this->checkRequired($data, $schema);

        $this->properties->validate($data, $schema);
    }

    /**
     * @param mixed $data
     */
    protected function checkMaxMin($data, stdClass $schema): void
    {
        // maxProperties
        $this->maxMin->validate($data, $schema, 'maxProperties');

        // minProperties
        $this->maxMin->validate($data, $schema, 'minProperties');
    }

    /**
     * @param mixed $data
     */
    protected function checkRequired($data, stdClass $schema): void
    {
        if (!$this->getValue($schema, 'required', $value, 'array')) {
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
