<?php

namespace JsonWorks\Tests\Constraint\Common;

class TypeTest extends \JsonWorks\Tests\Base
{
    public function testSimpleValid()
    {
        $schema = '{
            "type": ["string", "array", "number"]
        }';

        $data = array();

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

}

