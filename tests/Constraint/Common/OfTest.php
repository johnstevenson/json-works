<?php

namespace JsonWorks\Tests\Constraint\Common;

use JohnStevenson\JsonWorks\Schema\Constraints\Manager;
use JohnStevenson\JsonWorks\Schema\Constraints\OfConstraint;

class OfTest extends \JsonWorks\Tests\Base
{
    public function testInvalidSchemaArray1()
    {
        $schema = '{
            "allOf": {
                "type": "string",
                "enum": ["none", "value"]
            }
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testInvalidSchemaArray2()
    {
        $schema = '{
            "allOf": []
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testInvalidSchemaObject()
    {
        $schema = '{
            "not": ["type", "boolean"]
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }
}
