<?php

namespace JsonWorks\Tests\Constraint\Common;

use JohnStevenson\JsonWorks\Schema\Constraints\Manager;
use JohnStevenson\JsonWorks\Schema\Constraints\OfConstraint;

class OfTest extends \JsonWorks\Tests\Base
{
    protected $of;

    protected function setUp()
    {
        $manager = new Manager(false);
        $this->of = new OfConstraint($manager);
    }

    public function testInvalidSchemaArray()
    {
        $schema = '{
            "allOf": {
                "type": "string",
                "enum": ["none", "value"]
            }
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->of->check($data, $schema, 'allOf');
    }

    public function testInvalidSchemaObject()
    {
        $schema = '{
            "not": ["type", "boolean"]
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->of->check($data, $schema, 'not');
    }
}
