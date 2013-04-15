<?php

namespace JsonWorks\Tests\Constraint\Arrays;

class MaxItemsTest extends \JsonWorks\Tests\Base
{
    public function testDataValid()
    {
        $schema = '{
            "maxItems": 2
        }';

        $data = array(1, 2);

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testDataInvalid()
    {
        $schema = '{
            "maxItems": 2
        }';

        $data = array(1, 2, 3);

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
                    "maxItems": 2
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
                    "maxItems": 2
                }
            }
        }';

        $data = '{
            "items": ["item1", "item2", "item3"]
        }';

        $this->assertFalse($this->validate($schema, $data));
    }
}

