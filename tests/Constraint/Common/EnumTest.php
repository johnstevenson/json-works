<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Common;

class EnumTest extends \JsonWorks\Tests\Base
{
    public function testData(): void
    {
        $schema = '{
            "enum": ["one", "two", "three"]
        }';

        $data = 'two';
        self::assertTrue($this->validate($schema, $data), 'Valid');

        $data ='four';
        self::assertFalse($this->validate($schema, $data), 'Invalid');
    }

    public function testCompound(): void
    {
        $schema = '{
            "enum": ["one", {"name": "value"}, "three"]
        }';

        $data = '{"name": "value"}';
        self::assertTrue($this->validate($schema, $data), 'Valid');

        $data = '{"name1": "value1"}';
        self::assertFalse($this->validate($schema, $data), 'Invalid');
    }
}
