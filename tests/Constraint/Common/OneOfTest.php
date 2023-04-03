<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Common;

class OneOfTest extends \JsonWorks\Tests\Base
{
    public function testSimpleValid(): void
    {
        $schema = '{
            "oneOf": [
                {"type": "boolean"},
                {"enum": ["none", "value"]}
            ]
        }';

        $data = 'value';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testSimpleInvalid(): void
    {
        $schema = '{
            "oneOf": [
                {"type": "string"},
                {"enum": ["none", "value"]}
            ]
        }';

        $data = 'value';

        self::assertFalse($this->validate($schema, $data));
    }
}
