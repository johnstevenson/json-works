<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Strings;

class InvalidSchemaTest extends \JsonWorks\Tests\Base
{
    public function testMinLengthNotInteger1(): void
    {
        $schema = '{
            "minLength": "1"
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMinLengthNotInteger2(): void
    {
        $schema = '{
            "minLength": 1.0
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMinLengthNegative(): void
    {
        $schema = '{
            "minLength": -7
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxLengthNotInteger1(): void
    {
        $schema = '{
            "maxLength": "2"
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxLengthNotInteger2(): void
    {
        $schema = '{
            "maxLength": 2.0
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxLengthNegative(): void
    {
        $schema = '{
            "maxLength": -7
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testPatternNotString(): void
    {
        $schema = '{
            "pattern": true
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testPatternInvalidRegex(): void
    {
        $schema = '{
            "pattern": "(*)"
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testFormatNotString(): void
    {
        $schema = '{
            "format": {}
        }';

        $data = 'test';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }
}
