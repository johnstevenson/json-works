<?php

namespace JsonWorks\Tests\Constraint\Strings;

class PatternTest extends \JsonWorks\Tests\Base
{
    public function testValid()
    {
        $schema = '{
            "properties": {
                "test": {"pattern": "es"}
            }
        }';

        $data = '{
            "test": "test"
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testInvalid()
    {
        $schema = '{
            "properties": {
                "test": {"pattern": "ts"}
            }
        }';

        $data = '{
            "test": "test"
        }';

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testInvalidSchemaNotString()
    {
        $schema = '{
            "properties": {
                "test": {"pattern": 7}
            }
        }';

        $data = '{
            "test": "test"
        }';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }
}
