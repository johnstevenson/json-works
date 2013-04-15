<?php

namespace JsonWorks\Tests\Constraint\Strings;

class FormatTest extends \JsonWorks\Tests\Base
{
    public function testDateTimeValid()
    {
        $schema = '{
            "format": "date-time"
        }';

        $data = '2009-10-30 03:45:35';
        $this->assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:35';
        $this->assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30t03:45:35';
        $this->assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30t03:45:35z';
        $this->assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:35+01:30';
        $this->assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:35-01:30';
        $this->assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:35.1';
        $this->assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:35.1Z';
        $this->assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30 03:45:35.1+01:30';
        $this->assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30 03:45:35.1-01:30';
        $this->assertTrue($this->validate($schema, $data), 'Testing: '.$data);
     }

    public function testDateTimeInvalid()
    {
        $schema = '{
            "format": "date-time"
        }';

        $data = '2009-80-30 03:45:35';
        $this->assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T83:45:35';
        $this->assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:35X';
        $this->assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:3501:30';
        $this->assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:3501:30';
        $this->assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:35.18';
        $this->assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:351Z';
        $this->assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-70 03:45:35.1+01:30';
        $this->assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30 03:45:35.1Z-01:30';
        $this->assertFalse($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testEmailValid()
    {
        $schema = '{
            "format": "email"
        }';

        $data = 'person@somewhere.com';

        $this->assertTrue($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testEmailInvalid()
    {
        $schema = '{
            "format": "email"
        }';

        $data = 'person@somewhere';

        $this->assertFalse($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testHostnameValid()
    {
        $schema = '{
            "format": "hostname"
        }';

        $data = 'sub.example.com';

        $this->assertTrue($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testHostnameInvalid()
    {
        $schema = '{
            "format": "hostname"
        }';

        $data = 'localhost';

        $this->assertFalse($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testIpv4Valid()
    {
        $schema = '{
            "format": "ipv4"
        }';

        $data = '178.10.1.2';

        $this->assertTrue($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testIpv4Invalid()
    {
        $schema = '{
            "format": "ipv4"
        }';

        $data = '256.10.1.2';

        $this->assertFalse($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testIpv6Valid()
    {
        $schema = '{
            "format": "ipv6"
        }';

        $data = '::ff';

        $this->assertTrue($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testIpv6Invalid()
    {
        $schema = '{
            "format": "ipv6"
        }';

        $data = ':::ff';

        $this->assertFalse($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testUriValid()
    {
        $schema = '{
            "format": "uri"
        }';

        $data = 'http://sub.example.com';

        $this->assertTrue($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testUriInvalid()
    {
        $schema = '{
            "format": "uri"
        }';

        $data = 'http//sub.example.com';

        $this->assertFalse($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testUnknownInvalid()
    {
        $schema = '{
            "format": "my-format"
        }';

        $data = 'test';

        $this->assertFalse($this->validate($schema, $data), 'Testing: '.$data);
    }
}

