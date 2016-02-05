<?php

namespace JsonWorks\Tests\Constraint\Common;

class TypeTest extends \JsonWorks\Tests\Base
{
    public function testSimpleValid()
    {
        $schema = '{
            "type": ["string", "array", "number"]
        }';

        $data = [];

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testSimpleInvalid()
    {
        $schema = '{
            "type": ["string", "array", "number"]
        }';

        $data = false;

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testCompoundValid()
    {
        $schema = '{
            "type": "object"
        }';

        $data = '{"name": "value"}';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testCompoundInvalid()
    {
        $schema = '{
            "type": "array"
        }';

        $data = '{"name1": "value1"}';

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testEmptyArrayValid()
    {
        $schema = '{
            "type": []
        }';

        $data = 'two';
        $this->assertTrue($this->validate($schema, $data));
    }

    public function testInvalidSchemaWrongType()
    {
        $schema = '{
            "type": {}
        }';

        $data = 'two';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testInvalidSchemaEmpty()
    {
        $schema = '{
            "type": ""
        }';

        $data = 'two';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }



    public function testInvalidSchemaDuplicates()
    {
        $schema = '{
            "type": ["string", "string", "number"]
        }';

        $data = 'two';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testInvalidSchemaUnknownType()
    {
        $schema = '{
            "type": ["string", "array", "unknown", "number"]
        }';

        $data = 'two';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }
}
