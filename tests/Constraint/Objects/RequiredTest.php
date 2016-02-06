<?php

namespace JsonWorks\Tests\Constraint\Objects;

class RequiredTest extends \JsonWorks\Tests\Base
{
    public function testSimpleValid()
    {
        $schema = '{
            "required": ["prop1", "prop3"]
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2,
            "prop3": 3
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testSimpleInvalid()
    {
        $schema = '{
            "required": ["prop1", "prop3"]
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2,
            "prop4": 4
        }';

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testInvalidSchemaNotArray()
    {
        $schema = '{
            "required": true
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2,
            "prop3": 3
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testInvalidSchemaEmptyArray()
    {
        $schema = '{
            "required": []
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2,
            "prop3": 3
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testInvalidSchemaNotStringArray()
    {
        $schema = '{
            "required": ["prop1", 0, "prop3"]
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2,
            "prop3": 3
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testInvalidSchemaNotUniqueArray()
    {
        $schema = '{
            "required": ["prop1", "prop3", "prop1", "prop3"]
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2,
            "prop3": 3
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }
}
