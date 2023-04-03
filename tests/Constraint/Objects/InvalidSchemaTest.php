<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Objects;

class InvalidSchemaTest extends \JsonWorks\Tests\Base
{
    public function testMinPropertiesNotInteger1(): void
    {
        $schema = '{
            "minProperties": "1"
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMinPropertiesNotInteger2(): void
    {
        $schema = '{
            "minProperties": 2.0
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMinPropertiesNegative(): void
    {
        $schema = '{
            "minProperties": -6
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxPropertiesNotInteger1(): void
    {
        $schema = '{
            "maxProperties": "1"
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxPropertiesNotInteger2(): void
    {
        $schema = '{
            "maxProperties": 2.0
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxPropertiesNegative(): void
    {
        $schema = '{
            "maxProperties": -6
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testRequiredNotArray(): void
    {
        $schema = '{
            "required": "prop1"
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testRequiredEmptyArray(): void
    {
        $schema = '{
            "required": []
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2,
            "prop3": 3
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testRequiredNotStringArray(): void
    {
        $schema = '{
            "required": ["prop1", 0, "prop3"]
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2,
            "prop3": 3
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testRequiredNotUniqueArray(): void
    {
        $schema = '{
            "required": ["prop1", "prop3", "prop1", "prop3"]
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2,
            "prop3": 3
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testAdditionalNotObjectOrBoolean(): void
    {
        $schema = '{
            "additionalProperties": 1
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testPropertiesNotObject(): void
    {
        $schema = '{
            "properties": [
                true
            ]
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testPropertyValueNotObject(): void
    {
        $schema = '{
            "properties": {
                "prop1": {},
                "prop2": 3
            },
            "additionalProperties": true
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testPatternPropertiesNotObject(): void
    {
        $schema = '{
            "patternProperties": [
                {"prop1": {}}
            ]
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testPatternPropertyValueNotObject(): void
    {
        $schema = '{
            "patternProperties": {
                "prop1": {},
                "prop2": 3
            },
            "additionalProperties": true
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->expectException('RuntimeException');
        $this->validate($schema, $data);
    }
}
