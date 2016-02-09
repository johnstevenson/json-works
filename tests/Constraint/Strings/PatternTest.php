<?php

namespace JsonWorks\Tests\Constraint\Strings;

class PatternTest extends \JsonWorks\Tests\Base
{
    public function testValid()
    {
        $schema = '{
            "pattern": "es"
        }';

        $data = 'test';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testInvalid()
    {
        $schema = '{
            "pattern": "ts"
        }';

        $data = 'test';

        $this->assertFalse($this->validate($schema, $data));
    }
}
