<?php

namespace JsonWorks\Tests\Constraint\Objects;

class MinPropertiesTest extends \JsonWorks\Tests\Base
{
    public function testSimpleValid()
    {
        $schema = '{
            "minProperties": 1
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
            "minProperties": 2
        }';

        $data = '{
            "prop1": 1
        }';

        $this->assertFalse($this->validate($schema, $data));
    }

}

