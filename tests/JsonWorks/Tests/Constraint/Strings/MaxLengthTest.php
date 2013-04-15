<?php

namespace JsonWorks\Tests\Constraint\Strings;

class MaxLengthTest extends \JsonWorks\Tests\Base
{
    public function testValid()
    {
        $schema = '{
            "properties": {
                "test": {"maxLength": 5}
            }
        }';

        $data = '{
            "test": "test"
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testInvalid()
    {
        $schema = '{
            "properties": {
                "test": {"maxLength": 5}
            }
        }';

        $data = '{
            "test": "test string"
        }';

        $this->assertFalse($this->validate($schema, $data));
    }
}

