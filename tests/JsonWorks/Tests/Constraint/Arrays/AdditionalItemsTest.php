<?php

namespace JsonWorks\Tests\Constraint\Arrays;

class AdditionalItemsTest extends \JsonWorks\Tests\Base
{
    public function testDataNoneValid()
    {
        $schema = '{}';

        $data = array(1, 2);

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testDataTrueValid()
    {
        $schema = '{
            "additionalItems": true
        }';

        $data = array(1, 2);

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testDataFalseValid()
    {
        $schema = '{
            "items": {
                "type": "integer"
            },
            "additionalItems": false
        }';

        $data = array(1, 2);

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testDataObjectValid()
    {
        $schema = '{
            "additionalItems": {}
        }';

        $data = array(1, 2);

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testDataInvalid()
    {
        $schema = '{
            "items": [],
            "additionalItems": false
        }';

        $data = array(1, 2);

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testExample1Valid()
    {
        $schema = '{
            "items": [ {}, {}, {} ],
            "additionalItems": false
        }';

        $data = '[]';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testExample2Valid()
    {
        $schema = '{
            "items": [ {}, {}, {} ],
            "additionalItems": false
        }';

        $data = '[ [ 1, 2, 3, 4 ], [ 5, 6, 7, 8 ] ]';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testExample3Valid()
    {
        $schema = '{
            "items": [ {}, {}, {} ],
            "additionalItems": false
        }';

        $data = '[ 1, 2, 3 ]';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testExample1Invalid()
    {
        $schema = '{
            "items": [ {}, {}, {} ],
            "additionalItems": false
        }';

        $data = '[ 1, 2, 3, 4 ]';

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testExample2Invalid()
    {
        $schema = '{
            "items": [ {}, {}, {} ],
            "additionalItems": false
        }';

        $data = '[ null, { "a": "b" }, true, 31.000002020013 ]';

        $this->assertFalse($this->validate($schema, $data));
    }

}

