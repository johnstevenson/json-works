<?php

namespace JsonWorks\Tests\Constraint\Arrays;

class InvalidSchemaTest extends \JsonWorks\Tests\Base
{
    public function testMinItemsString()
    {
        $schema = '{
            "minItems": "1"
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

    public function testMaxItemsString()
    {
        $schema = '{
            "maxItems": "1"
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
}
