<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Arrays;

class MinItemsTest extends \JsonWorks\Tests\Base
{
    public function testDataValid(): void
    {
        $schema = '{
            "minItems": 1
        }';

        $data = array(1, 2);

        self::assertTrue($this->validate($schema, $data));
    }

    public function testDataInvalid(): void
    {
        $schema = '{
            "minItems": 3
        }';

        $data = array(1, 2);

        self::assertFalse($this->validate($schema, $data));
    }

    public function testValid(): void
    {
        $schema = '{
            "properties": {
                "items": {
                    "items": {
                        "type": "string"
                    },
                    "minItems": 1
                }
            }
        }';

        $data = '{
            "items": ["item1", "item2"]
        }';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testInvalid(): void
    {
        $schema = '{
            "properties": {
                "items": {
                    "items": {
                        "type": "string"
                    },
                    "minItems": 3
                }
            }
        }';

        $data = '{
            "items": ["item1", "item2"]
        }';

        self::assertFalse($this->validate($schema, $data));
    }
}
