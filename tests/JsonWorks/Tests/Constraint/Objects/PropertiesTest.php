<?php

namespace JsonWorks\Tests\Constraint\Objects;

class PropertiesTest extends \JsonWorks\Tests\Base
{
    public function testSimpleValid()
    {
        $schema = '{
            "properties": {
                "p1": {}
            },
            "patternProperties": {
                "p": {},
                "[0-9]": {}
            },
            "additionalProperties": false
        }';

        $data = '{
            "p1": true,
            "p2": null,
            "a32&o": "foobar",
            "apple": "pie"
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testSimpleInvalid()
    {
        $schema = '{
            "properties": {
                "p1": {}
            },
            "patternProperties": {
                "p": {},
                "[0-9]": {}
            },
            "additionalProperties": false
        }';

        $data = '{
            "p1": true,
            "p2": null,
            "a32&o": "foobar",
            "": [],
            "fiddle": 42,
            "apple": "pie"
        }';

        $this->assertFalse($this->validate($schema, $data));
    }

}

