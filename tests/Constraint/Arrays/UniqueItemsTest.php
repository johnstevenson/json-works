<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Arrays;

class UniqueItemsTest extends \JsonWorks\Tests\Base
{
    public function testDataTrueValid(): void
    {
        $schema = '{
            "uniqueItems": true
        }';

        $data = array(1, null, 2, false, 3);

        self::assertTrue($this->validate($schema, $data));
    }

    public function testDataTrueInvalid(): void
    {
         $schema = '{
            "uniqueItems": true
        }';

        $data = array(1, null, 2, false, 3, 1);

        self::assertFalse($this->validate($schema, $data));
    }

    public function testDataFalseValid(): void
    {
         $schema = '{
            "uniqueItems": false
        }';

        $data = array(1, null, 2, false, 3, 1);

        self::assertTrue($this->validate($schema, $data));
    }

    public function testTrueValid(): void
    {
        $schema = '{
            "properties": {
                "items": {
                    "items": {
                        "type": "string"
                    },
                    "uniqueItems": true
                }
            }
        }';

        $data = '{
            "items": ["item1", "item2"]
        }';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testTrueInvalid(): void
    {
        $schema = '{
            "properties": {
                "items": {
                    "items": {
                        "type": "string"
                    },
                    "uniqueItems": true
                }
            }
        }';

        $data = '{
            "items": ["item1", "item2", "item1"]
        }';

        self::assertFalse($this->validate($schema, $data));
    }

    public function testFalseValid(): void
    {
        $schema = '{
            "properties": {
                "items": {
                    "items": {
                        "type": "string"
                    },
                    "uniqueItems": false
                }
            }
        }';

        $data = '{
            "items": ["item1", "item2", "item1"]
        }';

        self::assertTrue($this->validate($schema, $data));
    }
}
