<?php

namespace JsonWorks\Tests\Constraint\Objects;

class InvalidSchemaTest extends \JsonWorks\Tests\Base
{
    public function testMinPropertiesString()
    {
        $schema = '{
            "minProperties": "1"
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMinPropertiesNegative()
    {
        $schema = '{
            "minProperties": -6
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxPropertiesString()
    {
        $schema = '{
            "maxProperties": "1"
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxPropertiesNegative()
    {
        $schema = '{
            "maxProperties": -6
        }';

        $data = '{
            "prop1": 1,
            "prop2": 2
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }
}
