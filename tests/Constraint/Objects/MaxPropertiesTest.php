<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Objects;

class MaxPropertiesTest extends \JsonWorks\Tests\Base
{
    public function testSimpleValid(): void
    {
        $schema = '{
            "maxProperties": 2
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testSimpleInvalid(): void
    {
        $schema = '{
            "maxProperties": 2
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2,
            "prop3": 3
        }';

        self::assertFalse($this->validate($schema, $data));
    }
}
