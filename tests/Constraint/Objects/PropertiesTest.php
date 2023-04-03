<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Objects;

class PropertiesTest extends \JsonWorks\Tests\Base
{
    public function testSimpleValid(): void
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

        self::assertTrue($this->validate($schema, $data));
    }

    public function testSimpleInvalid(): void
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

        self::assertFalse($this->validate($schema, $data));
    }
}
