<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Objects;

class MinPropertiesTest extends \JsonWorks\Tests\Base
{
    public function testSimpleValid(): void
    {
        $schema = '{
            "minProperties": 1
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
            "minProperties": 2
        }';

        $data = '{
            "prop1": 1
        }';

        self::assertFalse($this->validate($schema, $data));
    }
}
