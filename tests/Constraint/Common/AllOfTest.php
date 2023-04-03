<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Common;

class AllOfTest extends \JsonWorks\Tests\Base
{
    public function testDataValid(): void
    {
        $schema = '{
            "allOf": [
                {"type": "string"},
                {"enum": ["none", "value"]}
            ]
        }';

        $data = 'value';
        self::assertTrue($this->validate($schema, $data));
    }

    public function testDataInvalid(): void
    {
        $schema = '{
            "allOf": [
                {"type": "string"},
                {"enum": ["none", "value"]}
            ]
        }';

        $data = 'other';
        self::assertFalse($this->validate($schema, $data));
    }

    public function testValid(): void
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
        self::assertTrue($this->validate($schema, $data));
    }

    public function testInvalid(): void
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
        self::assertFalse($this->validate($schema, $data));
    }
}
