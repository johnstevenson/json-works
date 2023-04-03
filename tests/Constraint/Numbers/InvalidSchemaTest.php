<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Numbers;

class InvalidSchemaTest extends \JsonWorks\Tests\Base
{
    public function testMinimumNotNumber(): void
    {
        $schema = '{
            "properties": {
                "test": {"minimum": true}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testExclusiveNoMinimum(): void
    {
        $schema = '{
            "properties": {
                "test": {"exclusiveMinimum": true}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaximumNotNumber(): void
    {
        $schema = '{
            "properties": {
                "test": {"maximum": true}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testExclusiveNoMaximum(): void
    {
        $schema = '{
            "properties": {
                "test": {"exclusiveMaximum": true}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMultipleOfZeroValue(): void
    {
        $schema = '{
            "properties": {
                "test": {"multipleOf": 0}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMultipleOfNegativeValue(): void
    {
        $schema = '{
            "properties": {
                "test": {"multipleOf": -0.87532}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }
}
