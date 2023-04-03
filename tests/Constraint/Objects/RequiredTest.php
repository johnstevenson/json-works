<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Objects;

class RequiredTest extends \JsonWorks\Tests\Base
{
    public function testSimpleValid(): void
    {
        $schema = '{
            "required": ["prop1", "prop3"]
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2,
            "prop3": 3
        }';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testSimpleInvalid(): void
    {
        $schema = '{
            "required": ["prop1", "prop3"]
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2,
            "prop4": 4
        }';

        self::assertFalse($this->validate($schema, $data));
    }
}
