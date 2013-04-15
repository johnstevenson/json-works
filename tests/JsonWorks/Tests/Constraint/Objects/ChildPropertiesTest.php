<?php

namespace JsonWorks\Tests\Constraint\Objects;

class ChildPropertiesTest extends \JsonWorks\Tests\Base
{
    public function testSimpleValid()
    {
        $schema = '{
            "properties": {
                "prop1": {"type": "integer"}
            }
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
            "properties": {
                "prop1": {"type": "integer"}
            }
        }';

        $data = '{
            "prop1": "one",
            "prop2": 2
        }';

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testValid()
    {
        $schema = '{
            "properties": {
                "prop1": {
                    "properties": {
                        "sub1": {"type": "integer"}
                    }
                }
            }
        }';

        $data = '{
            "prop1": {
                "sub1": 1
            },
            "prop2": 2
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testInvalid()
    {
        $schema = '{
            "properties": {
                "prop1": {
                    "properties": {
                        "sub1": {"type": "integer"}
                    }
                }
            }
        }';

        $data = '{
            "prop1": {
                "sub1": "one"
            },
            "prop2": 2
        }';

        $this->assertFalse($this->validate($schema, $data));
    }
}

