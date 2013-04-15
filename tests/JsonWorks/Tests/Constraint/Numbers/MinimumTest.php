<?php

namespace JsonWorks\Tests\Constraint\Numbers;

class MinimumTest extends \JsonWorks\Tests\Base
{
    public function testIntegerValid()
    {
        $schema = '{
            "properties": {
                "test": {"minimum": 5}
            }
        }';

        $data = '{
            "test": 5
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testIntegerInvalid()
    {
        $schema = '{
            "properties": {
                "test": {"minimum": 5}
            }
        }';

        $data = '{
            "test": 4
        }';

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testFloatValid()
    {
        $schema = '{
            "properties": {
                "test": {"minimum": 5.4}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testFloatInvalid()
    {
        $schema = '{
            "properties": {
                "test": {"minimum": 5.4}
            }
        }';

        $data = '{
            "test": 5.3999
        }';

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testIntegerExclusiveValid()
    {
        $schema = '{
            "properties": {
                "test": {"minimum": 5, "exclusiveMinimum": true}
            }
        }';

        $data = '{
            "test": 6
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testIntegerExclusiveInvalid()
    {
        $schema = '{
            "properties": {
                "test": {"minimum": 5, "exclusiveMinimum": true}
            }
        }';

        $data = '{
            "test": 5
        }';

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testFloatExclusiveValid()
    {
        $schema = '{
            "properties": {
                "test": {"minimum": 5.4, "exclusiveMinimum": true}
            }
        }';

        $data = '{
            "test": 5.41
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testFloatExclusiveInvalid()
    {
        $schema = '{
            "properties": {
                "test": {"minimum": 5.4, "exclusiveMinimum": true}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        $this->assertFalse($this->validate($schema, $data));
    }
}

