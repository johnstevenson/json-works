<?php

namespace JsonWorks\Tests\Constraint\Common;

class EnumTest extends \JsonWorks\Tests\Base
{
    public function testData()
    {
        $schema = '{
            "enum": ["one", "two", "three"]
        }';

        $data = 'two';
        $this->assertTrue($this->validate($schema, $data), 'Valid');

        $data ='four';
        $this->assertFalse($this->validate($schema, $data), 'Invalid');
    }

    public function testCompound()
    {
        $schema = '{
            "enum": ["one", {"name": "value"}, "three"]
        }';

        $data = '{"name": "value"}';
        $this->assertTrue($this->validate($schema, $data), 'Valid');

        $data = '{"name1": "value1"}';
        $this->assertFalse($this->validate($schema, $data), 'Invalid');
    }

    public function testInvalidSchemaWrongType()
    {
        $schema = '{
            "enum": {}
        }';

        $data = 'two';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testInvalidSchemaEmpty()
    {
        $schema = '{
            "enum": []
        }';

        $data = 'two';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testInvalidSchemaDuplicates()
    {
        $schema = '{
            "enum": ["one", "one", "three"]
        }';

        $data = 'two';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }
}
