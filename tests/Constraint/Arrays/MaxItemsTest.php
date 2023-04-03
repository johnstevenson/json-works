<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Arrays;

class MaxItemsTest extends \JsonWorks\Tests\Base
{
    public function testDataValid(): void
    {
        $schema = '{
            "maxItems": 2
        }';

        $data = array(1, 2);

        self::assertTrue($this->validate($schema, $data));
    }

    public function testDataInvalid(): void
    {
        $schema = '{
            "maxItems": 2
        }';

        $data = array(1, 2, 3);

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
                    "maxItems": 2
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
                    "maxItems": 2
                }
            }
        }';

        $data = '{
            "items": ["item1", "item2", "item3"]
        }';

        self::assertFalse($this->validate($schema, $data));
    }
}
