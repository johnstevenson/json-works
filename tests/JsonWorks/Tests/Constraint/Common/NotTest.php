<?php

namespace JsonWorks\Tests\Constraint\Common;

class NotTest extends \JsonWorks\Tests\Base
{
    public function testData()
    {
        $schema = '{
            "not": {"type": "boolean"}
        }';

        $data = 'value';
        $this->assertTrue($this->validate($schema, $data), 'Valid');

        $data = true;
        $this->assertFalse($this->validate($schema, $data), 'Invalid');
    }

}

