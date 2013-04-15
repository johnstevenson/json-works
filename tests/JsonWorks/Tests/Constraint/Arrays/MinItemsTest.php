<?php

namespace JsonWorks\Tests\Constraint\Arrays;

class MinItemsTest extends \JsonWorks\Tests\Base
{
    public function testDataValid()
    {
        $schema = '{
            "minItems": 1
        }';

        $data = array(1, 2);

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testDataInvalid()
    {
        $schema = '{
            "minItems": 3
        }';

        $data = array(1, 2);

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testValid()
    {
        $schema = '{
            "properties": {
                "items": {
                    "items": {
                        "type": "string"
                    },
                    "minItems": 1
                }
            }
        }';

        $data = '{
            "items": ["item1", "item2"]
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testInvalid()
    {
        $schema = '{
            "properties": {
                "items": {
                    "items": {
                        "type": "string"
                    },
                    "minItems": 3
                }
            }
        }';

        $data = '{
            "items": ["item1", "item2"]
        }';

        $this->assertFalse($this->validate($schema, $data));
    }
}

