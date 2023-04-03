<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Arrays;

class ItemsTest extends \JsonWorks\Tests\Base
{
    public function testObjectValid(): void
    {
        $schema = '{
            "items": {
                "type": "integer"
            },
            "additionalItems": false
        }';

        $data = '[1, 2, 5, 6]';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testObjectInvalid(): void
    {
        $schema = '{
            "items": {
                "type": "integer"
            },
            "additionalItems": false
        }';

        $data = '[1, {}, 5, {}]';

        self::assertFalse($this->validate($schema, $data));
    }

    public function testArrayValid(): void
    {
        $schema = '{
            "items": [
                {
                    "type": "integer"
                },
                {
                    "type": "string"
                },
                {
                    "type": "object"
                }
            ],
            "additionalItems": false
        }';

        $data = '[ 1, "two", {"name": "value"} ]';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testArrayInvalid(): void
    {
        $schema = '{
            "items": [
                {
                    "type": "integer"
                },
                {
                    "type": "string"
                },
                {
                    "type": "object"
                }
            ],
            "additionalItems": false
        }';

        $data = '[ 1, "two", "three" ]';

        self::assertFalse($this->validate($schema, $data));
    }

    public function testAdditionalValid(): void
    {
        $schema = '{
            "items": [
                {
                    "type": "integer"
                },
                {
                    "type": "string"
                },
                {
                    "type": "object"
                }
            ],
            "additionalItems": {
                "type": "number"
            }
        }';

        $data = '[ 1, "two", {"name": "value"}, 24.3, 6, 9 ]';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testAdditionalInvalid(): void
    {
        $schema = '{
            "items": [
                {
                    "type": "integer"
                },
                {
                    "type": "string"
                },
                {
                    "type": "object"
                }
            ],
            "additionalItems": {
                "type": "number"
            }
        }';

        $data = '[ 1, "two", {"name": "value"}, null ]';

        self::assertFalse($this->validate($schema, $data));
    }
}
