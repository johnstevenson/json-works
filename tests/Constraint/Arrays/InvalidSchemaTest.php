<?php

namespace JsonWorks\Tests\Constraint\Arrays;

class InvalidSchemaTest extends \JsonWorks\Tests\Base
{
    public function testMinItemsNotInteger1()
    {
        $schema = '{
            "minItems": "1"
        }';

        $data = [1, 2];

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMinItemsNotInteger2()
    {
        $schema = '{
            "minItems": 1.0
        }';

        $data = [1, 2];

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMinItemsNegative()
    {
        $schema = '{
            "minItems": -7
        }';

        $data = [1, 2];

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxItemsNotInteger1()
    {
        $schema = '{
            "maxItems": "2"
        }';

        $data = [1, 2];

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxItemsNotInteger2()
    {
        $schema = '{
            "maxItems": 2.0
        }';

        $data = [1, 2];

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxItemsNegative()
    {
        $schema = '{
            "maxItems": -7
        }';

        $data = [1, 2];

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testAdditionalNotObjectOrBoolean()
    {
        $schema = '{
            "additionalItems": 1
        }';

        $data = [1, 2];

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testItemsNotArrayOrObject()
    {
        $schema = '{
            "items": true
        }';

        $data = [1, 2];

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testItemsArrayValueNotObject()
    {
        $schema = '{
            "items": [
                true
            ]
        }';

        $data = [1, 2];

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }
}
