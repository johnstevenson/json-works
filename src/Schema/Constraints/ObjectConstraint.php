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

use JohnStevenson\JsonWorks\Schema\Constraints\Manager;
use JohnStevenson\JsonWorks\Schema\Constraints\MaxMinConstraint;
use JohnStevenson\JsonWorks\Schema\Constraints\PropertiesConstraint;

class ObjectConstraint extends BaseConstraint
{
    protected $maxMin;
    protected $properties;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->maxMin = new MaxMinConstraint($manager);
        $this->properties = new PropertiesConstraint($manager);
    }

    public function validate($data, $schema)
    {
        // max and min
        $this->checkMaxMin($data, $schema);

        // required
        $this->checkRequired($data, $schema);

        $this->properties->validate($data, $schema);
    }

    protected function checkMaxMin($data, $schema)
    {
        // maxProperties
        $this->maxMin->validate($data, $schema, 'maxProperties');

        // minProperties
        $this->maxMin->validate($data, $schema, 'minProperties');
    }

    protected function checkRequired($data, $schema)
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
