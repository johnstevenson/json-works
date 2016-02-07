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

use JohnStevenson\JsonWorks\Schema\Constraints\ContainerConstraint;
use JohnStevenson\JsonWorks\Schema\Constraints\Manager;
use JohnStevenson\JsonWorks\Schema\Constraints\MaxMinConstraint;

class ObjectConstraint extends BaseConstraint
{
    protected $maxMin;
    protected $container;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->maxMin = new MaxMinConstraint($manager);
        $this->container = new ContainerConstraint($manager);
    }

    public function validate($data, $schema)
    {
        if (0 === count((array) $schema)) {
            return;
        }

        $this->checkCommon($data, $schema);

        $this->container->validate($data, $schema, 'properties');
    }

    protected function checkCommon($data, $schema)
    {
        // maxProperties
        $this->maxMin->validate($data, $schema, 'maxProperties');

        // minProperties
        $this->maxMin->validate($data, $schema, 'minProperties');

        // required
        $this->checkRequired($data, $schema);
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
