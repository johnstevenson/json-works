<?php

namespace JsonWorks\Tests\Constraint\Strings;

class MinLengthTest extends \JsonWorks\Tests\Base
{
    public function testValid()
    {
        $schema = '{
            "properties": {
                "test": {"minLength": 5}
            }
        }';

        $data = '{
            "test": "test string"
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testInvalid()
    {
        $schema = '{
            "properties": {
                "test": {"minLength": 5}
            }
        }';

        $data = '{
            "test": "test"
        }';

        $this->assertFalse($this->validate($schema, $data));
    }

}

