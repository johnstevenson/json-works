<?php

namespace JsonWorks\Tests\Constraint\Arrays;

class ItemsTest extends \JsonWorks\Tests\Base
{
    public function testObjectValid()
    {
        $schema = '{
            "items": {
                "type": "integer"
            },
            "additionalItems": false
        }';

        $data = '[1, 2, 5, 6]';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testObjectInvalid()
    {
        $schema = '{
            "items": {
                "type": "integer"
            },
            "additionalItems": false
        }';

        $data = '[1, {}, 5, 6]';

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testArrayValid()
    {
        $schema = '{
            "items": [
                {
                    "type": "integer"
                },
                {
                    "type": "string"
                },
                {
                    "type": "object"
                }
            ],
            "additionalItems": false
        }';

        $data = '[ 1, "two", {"name": "value"} ]';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testArrayInvalid()
    {
        $schema = '{
            "items": [
                {
                    "type": "integer"
                },
                {
                    "type": "string"
                },
                {
                    "type": "object"
                }
            ],
            "additionalItems": false
        }';

        $data = '[ 1, "two", "three" ]';

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testAdditionalValid()
    {
        $schema = '{
            "items": [
                {
                    "type": "integer"
                },
                {
                    "type": "string"
                },
                {
                    "type": "object"
                }
            ],
            "additionalItems": {
                "type": "number"
            }
        }';

        $data = '[ 1, "two", {"name": "value"}, 24.3, 6, 9 ]';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testAdditionalInvalid()
    {
        $schema = '{
            "items": [
                {
                    "type": "integer"
                },
                {
                    "type": "string"
                },
                {
                    "type": "object"
                }
            ],
            "additionalItems": {
                "type": "number"
            }
        }';

        $data = '[ 1, "two", {"name": "value"}, null ]';

        $this->assertFalse($this->validate($schema, $data));
    }
}

