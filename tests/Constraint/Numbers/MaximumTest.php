<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Numbers;

class MaximumTest extends \JsonWorks\Tests\Base
{
    public function testIntegerValid(): void
    {
        $schema = '{
            "properties": {
                "test": {"maximum": 5}
            }
        }';

        $data = '{
            "test": 5
        }';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testIntegerInvalid(): void
    {
        $schema = '{
            "properties": {
                "test": {"maximum": 5}
            }
        }';

        $data = '{
            "test": 6
        }';

        self::assertFalse($this->validate($schema, $data));
    }

    public function testFloatValid(): void
    {
        $schema = '{
            "properties": {
                "test": {"maximum": 5.4}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testFloatInvalid(): void
    {
        $schema = '{
            "properties": {
                "test": {"maximum": 5.4}
            }
        }';

        $data = '{
            "test": 5.41
        }';

        self::assertFalse($this->validate($schema, $data));
    }

    public function testIntegerExclusiveValid(): void
    {
        $schema = '{
            "properties": {
                "test": {"maximum": 5, "exclusiveMaximum": true}
            }
        }';

        $data = '{
            "test": 4
        }';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testIntegerExclusiveInvalid(): void
    {
        $schema = '{
            "properties": {
                "test": {"maximum": 5, "exclusiveMaximum": true}
            }
        }';

        $data = '{
            "test": 5
        }';

        self::assertFalse($this->validate($schema, $data));
    }

    public function testFloatExclusiveValid(): void
    {
        $schema = '{
            "properties": {
                "test": {"maximum": 5.4, "exclusiveMaximum": true}
            }
        }';

        $data = '{
            "test": 5.39
        }';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testFloatExclusiveInvalid(): void
    {
        $schema = '{
            "properties": {
                "test": {"maximum": 5.4, "exclusiveMaximum": true}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        self::assertFalse($this->validate($schema, $data));
    }
}
