<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Objects;

class ChildPropertiesTest extends \JsonWorks\Tests\Base
{
    public function testSimpleValid(): void
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

        self::assertTrue($this->validate($schema, $data));
    }

    public function testSimpleInvalid(): void
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

        self::assertFalse($this->validate($schema, $data));
    }

    public function testValid(): void
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

        self::assertTrue($this->validate($schema, $data));
    }

    public function testInvalid(): void
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

        self::assertFalse($this->validate($schema, $data));
    }
}
