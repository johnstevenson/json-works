<?php

namespace JsonWorks\Tests\Constraint\Strings;

class InvalidSchemaTest extends \JsonWorks\Tests\Base
{
    public function testMinLengthNotInteger1()
    {
        $schema = '{
            "minLength": "1"
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMinLengthNotInteger2()
    {
        $schema = '{
            "minLength": 1.0
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMinLengthNegative()
    {
        $schema = '{
            "minLength": -7
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxLengthNotInteger1()
    {
        $schema = '{
            "maxLength": "2"
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxLengthNotInteger2()
    {
        $schema = '{
            "maxLength": 2.0
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testMaxLengthNegative()
    {
        $schema = '{
            "maxLength": -7
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testPatternNotString()
    {
        $schema = '{
            "pattern": true
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testPatternInvalidRegex()
    {
        $schema = '{
            "pattern": "(*)"
        }';

        $data = 'value';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }

    public function testFormatNotString()
    {
        $schema = '{
            "format": {}
        }';

        $data = 'test';

        $this->setExpectedException('RuntimeException');
        $this->validate($schema, $data);
    }
}
