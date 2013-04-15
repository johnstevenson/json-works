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

}

