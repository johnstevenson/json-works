<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Strings;

class PatternTest extends \JsonWorks\Tests\Base
{
    public function testValid(): void
    {
        $schema = '{
            "pattern": "es"
        }';

        $data = 'test';

        self::assertTrue($this->validate($schema, $data));
    }

    public function testInvalid(): void
    {
        $schema = '{
            "pattern": "ts"
        }';

        $data = 'test';

        self::assertFalse($this->validate($schema, $data));
    }
}
