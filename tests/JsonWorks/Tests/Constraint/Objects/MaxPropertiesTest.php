<?php

namespace JsonWorks\Tests\Constraint\Objects;

class MaxPropertiesTest extends \JsonWorks\Tests\Base
{
    public function testSimpleValid()
    {
        $schema = '{
            "maxProperties": 2
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testSimpleInvalid()
    {
        $schema = '{
            "maxProperties": 2
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2,
            "prop3": 3
        }';

        $this->assertFalse($this->validate($schema, $data));
    }

}

