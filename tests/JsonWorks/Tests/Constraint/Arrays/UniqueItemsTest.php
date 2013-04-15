<?php

namespace JsonWorks\Tests\Constraint\Arrays;

class UniqueItemsTest extends \JsonWorks\Tests\Base
{
    public function testDataTrueValid()
    {
        $schema = '{
            "uniqueItems": true
        }';

        $data = array(1, null, 2, false, 3);

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testDataTrueInvalid()
    {
         $schema = '{
            "uniqueItems": true
        }';

        $data = array(1, null, 2, false, 3, 1);

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testDataFalseValid()
    {
         $schema = '{
            "uniqueItems": false
        }';

        $data = array(1, null, 2, false, 3, 1);

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testTrueValid()
    {
        $schema = '{
            "properties": {
                "items": {
                    "items": {
                        "type": "string"
                    },
                    "uniqueItems": true
                }
            }
        }';

        $data = '{
            "items": ["item1", "item2"]
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testTrueInvalid()
    {
        $schema = '{
            "properties": {
                "items": {
                    "items": {
                        "type": "string"
                    },
                    "uniqueItems": true
                }
            }
        }';

        $data = '{
            "items": ["item1", "item2", "item1"]
        }';

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testFalseValid()
    {
        $schema = '{
            "properties": {
                "items": {
                    "items": {
                        "type": "string"
                    },
                    "uniqueItems": false
                }
            }
        }';

        $data = '{
            "items": ["item1", "item2", "item1"]
        }';

        $this->assertTrue($this->validate($schema, $data));
    }
}

