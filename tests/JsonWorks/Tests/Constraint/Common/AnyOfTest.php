<?php

namespace JsonWorks\Tests\Constraint\Common;

class AnyOfTest extends \JsonWorks\Tests\Base
{
    public function testDataValid()
    {
        $schema = '{
            "anyOf": [
                {"type": "string"},
                {"type": "boolean"}
            ]
        }';

        $data = 'value';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testDataInvalid()
    {
        $schema = '{
            "anyOf": [
                {"type": "number"},
                {"type": "boolean"}
            ]
        }';

        $data = 'value';

        $this->assertFalse($this->validate($schema, $data));
    }

}

