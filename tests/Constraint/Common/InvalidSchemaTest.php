<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Common;

class InvalidSchemaTest extends \JsonWorks\Tests\Base
{
    public function testAllOfNotArray(): void
    {
        $schema = '{
            "allOf": {
                "type": "string"
            }
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testAllOfEmptyArray(): void
    {
        $schema = '{
            "allOf": []
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testAllOfItemNotObject(): void
    {
        $schema = '{
            "allOf": [
                true
            ]
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testAnyOfNotArray(): void
    {
        $schema = '{
            "anyOf": {
                "type": "string"
            }
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testAnyOfEmptyArray(): void
    {
        $schema = '{
            "anyOf": []
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testAnyOfItemNotObject(): void
    {
        $schema = '{
            "anyOf": [
                true
            ]
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testOneOfNotArray(): void
    {
        $schema = '{
            "oneOf": {
                "type": "string"
            }
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testOneOfEmptyArray(): void
    {
        $schema = '{
            "oneOf": []
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testOneOfItemNotObject(): void
    {
        $schema = '{
            "oneOf": [
                true
            ]
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testNotNotObject(): void
    {
        $schema = '{
            "not": true
        }';

        $data = 'value';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testEnumNotArray(): void
    {
        $schema = '{
            "enum": {}
        }';

        $data = 'two';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testEnumEmptyArray(): void
    {
        $schema = '{
            "enum": []
        }';

        $data = 'two';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testEnumNotUnique(): void
    {
        $schema = '{
            "enum": ["one", "one", "three"]
        }';

        $data = 'two';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testTypeNotStringOrArray(): void
    {
        $schema = '{
            "type": {}
        }';

        $data = 'two';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testTypeArrayNotStrings(): void
    {
        $schema = '{
            "type": ["string", [], "number"]
        }';

        $data = 'two';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testTypeArrayNotUnique(): void
    {
        $schema = '{
            "type": ["string", "string", "number"]
        }';

        $data = 'two';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testTypeArrayUnknownType(): void
    {
        $schema = '{
            "type": ["string", "array", "something", "number"]
        }';

        $data = 'two';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }


    public function testTypeArrayEmptyType(): void
    {
        $schema = '{
            "type": ""
        }';

        $data = 'two';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }
}
