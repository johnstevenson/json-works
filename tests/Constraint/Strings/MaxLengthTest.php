<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Strings;

class MaxLengthTest extends \JsonWorks\Tests\Base
{
    public function testValid(): void
    {
        $schema = '{
            "properties": {
                "test": {"maxLength": 5}
            }
        }';

        $data = '{
            "test": "test"
        }';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testInvalid(): void
    {
        $schema = '{
            "properties": {
                "test": {"maxLength": 5}
            }
        }';

        $data = '{
            "test": "test string"
        }';

        self::assertFalse($this->validate($schema, $data));
    }
}
