<?php

namespace JsonWorks\Tests\Constraint\Common;

class OneOfTest extends \JsonWorks\Tests\Base
{
    public function testSimpleValid()
    {
        $schema = '{
            "oneOf": [
                {"type": "boolean"},
                {"enum": ["none", "value"]}
            ]
        }';

        $data = 'value';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testSimpleInvalid()
    {
        $schema = '{
            "oneOf": [
                {"type": "string"},
                {"enum": ["none", "value"]}
            ]
        }';

        $data = 'value';

        $this->assertFalse($this->validate($schema, $data));
    }

}

