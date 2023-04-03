<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Common;

class AnyOfTest extends \JsonWorks\Tests\Base
{
    public function testDataValid(): void
    {
        $schema = '{
            "anyOf": [
                {"type": "string"},
                {"type": "boolean"}
            ]
        }';

        $data = 'value';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testDataInvalid(): void
    {
        $schema = '{
            "anyOf": [
                {"type": "number"},
                {"type": "boolean"}
            ]
        }';

        $data = 'value';

        self::assertFalse($this->validate($schema, $data));
    }
}
