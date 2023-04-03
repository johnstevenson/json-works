<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Strings;

class MinLengthTest extends \JsonWorks\Tests\Base
{
    public function testValid(): void
    {
        $schema = '{
            "properties": {
                "test": {"minLength": 5}
            }
        }';

        $data = '{
            "test": "test string"
        }';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testInvalid(): void
    {
        $schema = '{
            "properties": {
                "test": {"minLength": 5}
            }
        }';

        $data = '{
            "test": "test"
        }';

        self::assertFalse($this->validate($schema, $data));
    }
}
