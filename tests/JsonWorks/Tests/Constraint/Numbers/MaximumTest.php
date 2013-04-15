<?php

namespace JsonWorks\Tests\Constraint\Numbers;

class MaximumTest extends \JsonWorks\Tests\Base
{
    public function testIntegerValid()
    {
        $schema = '{
            "properties": {
                "test": {"maximum": 5}
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
                "test": {"maximum": 5}
            }
        }';

        $data = '{
            "test": 6
        }';

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testFloatValid()
    {
        $schema = '{
            "properties": {
                "test": {"maximum": 5.4}
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
                "test": {"maximum": 5.4}
            }
        }';

        $data = '{
            "test": 5.41
        }';

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testIntegerExclusiveValid()
    {
        $schema = '{
            "properties": {
                "test": {"maximum": 5, "exclusiveMaximum": true}
            }
        }';

        $data = '{
            "test": 4
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testIntegerExclusiveInvalid()
    {
        $schema = '{
            "properties": {
                "test": {"maximum": 5, "exclusiveMaximum": true}
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
                "test": {"maximum": 5.4, "exclusiveMaximum": true}
            }
        }';

        $data = '{
            "test": 5.39
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testFloatExclusiveInvalid()
    {
        $schema = '{
            "properties": {
                "test": {"maximum": 5.4, "exclusiveMaximum": true}
            }
        }';

        $data = '{
            "test": 5.4
        }';

        $this->assertFalse($this->validate($schema, $data));
    }
}

