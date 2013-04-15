<?php

namespace JsonWorks\Tests\Constraint\Common;

class AllOfTest extends \JsonWorks\Tests\Base
{
    public function testDataValid()
    {
        $schema = '{
            "allOf": [
                {"type": "string"},
                {"enum": ["none", "value"]}
            ]
        }';

        $data = 'value';
        $this->assertTrue($this->validate($schema, $data));
    }

    public function testDataInvalid()
    {
        $schema = '{
            "allOf": [
                {"type": "string"},
                {"enum": ["none", "value"]}
            ]
        }';

        $data = 'other';
        $this->assertFalse($this->validate($schema, $data));
    }

    public function testValid()
    {
        $schema = '{
            "properties": {
                "prop1": {
                    "allOf": [
                        {"$ref": "#/definitions/def1"},
                        {"$ref": "#/definitions/def2"}
                    ]
                }
            },
            "definitions": {
                "def1": {"type": "integer"},
                "def2": {"maximum": 5}
            }
        }';

        $data = '{"prop1": 5}';
        $this->assertTrue($this->validate($schema, $data));
    }

    public function testInvalid()
    {
        $schema = '{
            "properties": {
                "prop1": {
                    "allOf": [
                        {"$ref": "#/definitions/def1"},
                        {"$ref": "#/definitions/def2"}
                    ]
                }
            },
            "definitions": {
                "def1": {"type": "integer"},
                "def2": {"maximum": 5}
            }
        }';

        $data = '{"prop1": 6}';
        $this->assertFalse($this->validate($schema, $data));
    }
}

