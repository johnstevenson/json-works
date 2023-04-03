<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Common;

class TypeTest extends \JsonWorks\Tests\Base
{
    public function testSimpleValid(): void
    {
        $schema = '{
            "type": ["string", "array", "number"]
        }';

        $data = [];

        self::assertTrue($this->validate($schema, $data));
    }

    public function testSimpleInvalid(): void
    {
        $schema = '{
            "type": ["string", "array", "number"]
        }';

        $data = false;

        self::assertFalse($this->validate($schema, $data));
    }

    public function testCompoundValid(): void
    {
        $schema = '{
            "type": "object"
        }';

        $data = '{"name": "value"}';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testCompoundInvalid(): void
    {
        $schema = '{
            "type": "array"
        }';

        $data = '{"name1": "value1"}';

        self::assertFalse($this->validate($schema, $data));
    }

    public function testEmptyArrayValid(): void
    {
        $schema = '{
            "type": []
        }';

        $data = 'two';
        self::assertTrue($this->validate($schema, $data));
    }
}
