<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Common;

class NotTest extends \JsonWorks\Tests\Base
{
    public function testData(): void
    {
        $schema = '{
            "not": {"type": "boolean"}
        }';

        $data = 'value';
        self::assertTrue($this->validate($schema, $data), 'Valid');

        $data = true;
        self::assertFalse($this->validate($schema, $data), 'Invalid');
    }
}
