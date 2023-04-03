<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Numbers;

class MultipleOfTest extends \JsonWorks\Tests\Base
{
    public function testIntegerValid(): void
    {
        $schema = '{
            "properties": {
                "test": {"multipleOf": 5}
            }
        }';

        $data = '{
            "test": 25
        }';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testIntegerInvalid(): void
    {
        $schema = '{
            "properties": {
                "test": {"multipleOf": 5}
            }
        }';

        $data = '{
            "test": 26
        }';

        self::assertFalse($this->validate($schema, $data));
    }

    public function testFloatValid(): void
    {
        $schema = '{
            "properties": {
                "test": {"multipleOf": 5.4}
            }
        }';

        $data = '{
            "test": 54
        }';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testFloatInvalid(): void
    {
        $schema = '{
            "properties": {
                "test": {"multipleOf": 5.4}
            }
        }';

        $data = '{
            "test": 53.999
        }';

        self::assertFalse($this->validate($schema, $data));
    }
}
