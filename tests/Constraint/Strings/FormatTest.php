<?php declare(strict_types=1);

namespace JsonWorks\Tests\Constraint\Strings;

class FormatTest extends \JsonWorks\Tests\Base
{
    public function testDateTimeValid(): void
    {
        $schema = '{
            "format": "date-time"
        }';

        $data = '2009-10-30 03:45:35';
        self::assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:35';
        self::assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30t03:45:35';
        self::assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30t03:45:35z';
        self::assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:35+01:30';
        self::assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:35-01:30';
        self::assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:35.1';
        self::assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:35.1Z';
        self::assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30 03:45:35.1+01:30';
        self::assertTrue($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30 03:45:35.1-01:30';
        self::assertTrue($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testDateTimeInvalid(): void
    {
        $schema = '{
            "format": "date-time"
        }';

        $data = '2009-80-30 03:45:35';
        self::assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T83:45:35';
        self::assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:35X';
        self::assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:3501:30';
        self::assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:3501:30';
        self::assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:35.18';
        self::assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30T03:45:351Z';
        self::assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-70 03:45:35.1+01:30';
        self::assertFalse($this->validate($schema, $data), 'Testing: '.$data);

        $data = '2009-10-30 03:45:35.1Z-01:30';
        self::assertFalse($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testEmailValid(): void
    {
        $schema = '{
            "format": "email"
        }';

        $data = 'person@somewhere.com';

        self::assertTrue($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testEmailInvalid(): void
    {
        $schema = '{
            "format": "email"
        }';

        $data = 'person@somewhere';

        self::assertFalse($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testHostnameValid(): void
    {
        $schema = '{
            "format": "hostname"
        }';

        $data = 'sub.example.com';

        self::assertTrue($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testHostnameInvalid(): void
    {
        $schema = '{
            "format": "hostname"
        }';

        $data = 'localhost';

        self::assertFalse($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testIpv4Valid(): void
    {
        $schema = '{
            "format": "ipv4"
        }';

        $data = '178.10.1.2';

        self::assertTrue($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testIpv4Invalid(): void
    {
        $schema = '{
            "format": "ipv4"
        }';

        $data = '256.10.1.2';

        self::assertFalse($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testIpv6Valid(): void
    {
        $schema = '{
            "format": "ipv6"
        }';

        $data = '::ff';

        self::assertTrue($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testIpv6Invalid(): void
    {
        $schema = '{
            "format": "ipv6"
        }';

        $data = ':::ff';

        self::assertFalse($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testUriValid(): void
    {
        $schema = '{
            "format": "uri"
        }';

        $data = 'http://sub.example.com';

        self::assertTrue($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testUriInvalid(): void
    {
        $schema = '{
            "format": "uri"
        }';

        $data = 'http//sub.example.com';

        self::assertFalse($this->validate($schema, $data), 'Testing: '.$data);
    }

    public function testFormatUnknown(): void
    {
        $schema = '{
            "format": "my-format"
        }';

        $data = 'test';

        self::assertFalse($this->validate($schema, $data), 'Testing: '.$data);
    }
}
