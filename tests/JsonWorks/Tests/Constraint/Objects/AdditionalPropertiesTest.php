<?php

namespace JsonWorks\Tests\Constraint\Objects;

class AdditionalPropertiesTest extends \JsonWorks\Tests\Base
{
    public function testSimpleNoneValid()
    {
        $schema = '{}';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testSimpleTrueValid()
    {
        $schema = '{
            "additionalProperties": true
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testSimpleObjectValid()
    {
        $schema = '{
            "additionalProperties": {}
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
            "additionalProperties": false
        }';

        $data = '{
            "prop1": 1
        }';

        $this->assertFalse($this->validate($schema, $data));
    }

}

