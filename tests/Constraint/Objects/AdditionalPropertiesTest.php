<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Objects;

class AdditionalPropertiesTest extends \JsonWorks\Tests\Base
{
    public function testSimpleNoneValid(): void
    {
        $schema = '{}';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testSimpleTrueValid(): void
    {
        $schema = '{
            "additionalProperties": true
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testSimpleObjectValid(): void
    {
        $schema = '{
            "additionalProperties": {}
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
            "additionalProperties": false
        }';

        $data = '{
            "prop1": 1
        }';

        self::assertFalse($this->validate($schema, $data));
    }
}
