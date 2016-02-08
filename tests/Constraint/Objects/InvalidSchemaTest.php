<?php

namespace JsonWorks\Tests\Constraint\Objects;

class InvalidSchemaTest extends \JsonWorks\Tests\Base
{
    public function testMinPropertiesNotInteger1()
    {
        $schema = '{
            "minProperties": "1"
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMinPropertiesNotInteger2()
    {
        $schema = '{
            "minProperties": 2.0
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMinPropertiesNegative()
    {
        $schema = '{
            "minProperties": -6
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxPropertiesNotInteger1()
    {
        $schema = '{
            "maxProperties": "1"
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxPropertiesNotInteger2()
    {
        $schema = '{
            "maxProperties": 2.0
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxPropertiesNegative()
    {
        $schema = '{
            "maxProperties": -6
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testRequiredNotArray()
    {
        $schema = '{
            "required": "prop1"
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testAdditionalNotObjectOrBoolean()
    {
        $schema = '{
            "additionalProperties": 1
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testPropertiesNotObject()
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

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testPropertyValueNotObject()
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

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testPatternPropertiesNotObject()
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

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testPatternPropertyValueNotObject()
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

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }
}
