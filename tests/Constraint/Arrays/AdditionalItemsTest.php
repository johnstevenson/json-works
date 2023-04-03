<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Arrays;

class AdditionalItemsTest extends \JsonWorks\Tests\Base
{
    public function testDataNoneValid(): void
    {
        $schema = '{}';

        $data = [1, 2];

        self::assertTrue($this->validate($schema, $data));
    }

    public function testDataTrueValid(): void
    {
        $schema = '{
            "additionalItems": true
        }';

        $data = [1, 2];

        self::assertTrue($this->validate($schema, $data));
    }

    public function testDataFalseValid(): void
    {
        $schema = '{
            "items": {
                "type": "integer"
            },
            "additionalItems": false
        }';

        $data = [1, 2];

        self::assertTrue($this->validate($schema, $data));
    }

    public function testDataObjectValid(): void
    {
        $schema = '{
            "additionalItems": {}
        }';

        $data = [1, 2];

        self::assertTrue($this->validate($schema, $data));
    }

    public function testDataInvalid(): void
    {
        $schema = '{
            "items": [],
            "additionalItems": false
        }';

        $data = [1, 2];

        self::assertFalse($this->validate($schema, $data));
    }

    public function testExample1Valid(): void
    {
        $schema = '{
            "items": [ {}, {}, {} ],
            "additionalItems": false
        }';

        $data = [];

        self::assertTrue($this->validate($schema, $data));
    }

    public function testExample2Valid(): void
    {
        $schema = '{
            "items": [ {}, {}, {} ],
            "additionalItems": false
        }';

        $data = [ [ 1, 2, 3, 4 ], [ 5, 6, 7, 8 ] ];

        self::assertTrue($this->validate($schema, $data));
    }

    public function testExample3Valid(): void
    {
        $schema = '{
            "items": [ {}, {}, {} ],
            "additionalItems": false
        }';

        $data = [1, 2, 3];

        self::assertTrue($this->validate($schema, $data));
    }

    public function testExample1Invalid(): void
    {
        $schema = '{
            "items": [ {}, {}, {} ],
            "additionalItems": false
        }';

        $data = [1, 2, 3, 4];

        self::assertFalse($this->validate($schema, $data));
    }

    public function testExample2Invalid(): void
    {
        $schema = '{
            "items": [ {}, {}, {} ],
            "additionalItems": false
        }';

        $data = '[ null, { "a": "b" }, true, 31.000002020013 ]';

        self::assertFalse($this->validate($schema, $data));
    }
}
