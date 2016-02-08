<?php

namespace JsonWorks\Tests\Constraint\Common;

class InvalidSchemaTest extends \JsonWorks\Tests\Base
{
    public function testAllOfNotArray()
    {
        $schema = '{
            "allOf": {
                "type": "string"
            }
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testAllOfEmptyArray()
    {
        $schema = '{
            "allOf": []
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testAllOfItemNotObject()
    {
        $schema = '{
            "allOf": [
                true
            ]
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testAnyOfNotArray()
    {
        $schema = '{
            "anyOf": {
                "type": "string"
            }
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testAnyOfEmptyArray()
    {
        $schema = '{
            "anyOf": []
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testAnyOfItemNotObject()
    {
        $schema = '{
            "anyOf": [
                true
            ]
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testOneOfNotArray()
    {
        $schema = '{
            "oneOf": {
                "type": "string"
            }
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testOneOfEmptyArray()
    {
        $schema = '{
            "oneOf": []
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testOneOfItemNotObject()
    {
        $schema = '{
            "oneOf": [
                true
            ]
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testNotNotObject()
    {
        $schema = '{
            "not": true
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testEnumNotArray()
    {
        $schema = '{
            "enum": {}
        }';

        $data = 'two';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testEnumEmptyArray()
    {
        $schema = '{
            "enum": []
        }';

        $data = 'two';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testEnumNotUnique()
    {
        $schema = '{
            "enum": ["one", "one", "three"]
        }';

        $data = 'two';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testTypeNotStringOrArray()
    {
        $schema = '{
            "type": {}
        }';

        $data = 'two';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testTypeArrayNotStrings()
    {
        $schema = '{
            "type": ["string", [], "number"]
        }';

        $data = 'two';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testTypeArrayNotUnique()
    {
        $schema = '{
            "type": ["string", "string", "number"]
        }';

        $data = 'two';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testTypeArrayUnknownType()
    {
        $schema = '{
            "type": ["string", "array", "something", "number"]
        }';

        $data = 'two';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }


    public function testTypeArrayEmptyType()
    {
        $schema = '{
            "type": ""
        }';

        $data = 'two';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }
}
