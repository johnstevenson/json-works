<?php

namespace JsonWorks\Tests\Constraint\Numbers;

class InvalidSchemaTest extends \JsonWorks\Tests\Base
{
    public function testMinimumNotNumber()
    {
        $schema = '{
            "properties": {
                "test": {"minimum": true}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testExclusiveNoMinimum()
    {
        $schema = '{
            "properties": {
                "test": {"exclusiveMinimum": true}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaximumNotNumber()
    {
        $schema = '{
            "properties": {
                "test": {"maximum": true}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testExclusiveNoMaximum()
    {
        $schema = '{
            "properties": {
                "test": {"exclusiveMaximum": true}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMultipleOfZeroValue()
    {
        $schema = '{
            "properties": {
                "test": {"multipleOf": 0}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMultipleOfNegativeValue()
    {
        $schema = '{
            "properties": {
                "test": {"multipleOf": -0.87532}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }
}
