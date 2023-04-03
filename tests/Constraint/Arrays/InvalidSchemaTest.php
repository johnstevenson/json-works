<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Arrays;

class InvalidSchemaTest extends \JsonWorks\Tests\Base
{
    public function testMinItemsNotInteger1(): void
    {
        $schema = '{
            "minItems": "1"
        }';

        $data = [1, 2];

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMinItemsNotInteger2(): void
    {
        $schema = '{
            "minItems": 1.0
        }';

        $data = [1, 2];

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMinItemsNegative(): void
    {
        $schema = '{
            "minItems": -7
        }';

        $data = [1, 2];

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxItemsNotInteger1(): void
    {
        $schema = '{
            "maxItems": "2"
        }';

        $data = [1, 2];

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxItemsNotInteger2(): void
    {
        $schema = '{
            "maxItems": 2.0
        }';

        $data = [1, 2];

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxItemsNegative(): void
    {
        $schema = '{
            "maxItems": -7
        }';

        $data = [1, 2];

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testAdditionalNotObjectOrBoolean(): void
    {
        $schema = '{
            "additionalItems": 1
        }';

        $data = [1, 2];

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testItemsNotArrayOrObject(): void
    {
        $schema = '{
            "items": true
        }';

        $data = [1, 2];

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testItemsArrayValueNotObject(): void
    {
        $schema = '{
            "items": [
                true
            ]
        }';

        $data = [1, 2];

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }
}
