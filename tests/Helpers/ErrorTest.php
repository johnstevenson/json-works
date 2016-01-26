<?php

namespace JsonWorks\Tests\Helpers;

use JohnStevenson\JsonWorks\Helpers\Error;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    protected $error;

    protected function setUp()
    {
        $this->error = new Error();
    }

    public function testKnownCodeWithMsg()
    {
        $code = Error::ERR_PATH_KEY;
        $msg = 'prop1/inner1';

        $result = $this->error->get($code, $msg);
        $this->assertStringStartsWith('ERR_PATH_KEY', $result);
        $this->assertStringEndsWith('[prop1/inner1]', $result);
    }

    public function testKnownCodeNoMsg()
    {
        $code = Error::ERR_PATH_KEY;

        $result = $this->error->get($code);
        $this->assertStringStartsWith('ERR_PATH_KEY', $result);
        $this->assertStringNotMatchesFormat('%a [%s]', $result);
    }

    public function testUnknownCodeWithMsg()
    {
        // We use null to represent an Unknown code
        $code = null;
        $msg = 'prop1/inner1';

        $result = $this->error->get($code, $msg);
        $this->assertStringStartsWith('ERR_UNKNOWN', $result);
        $this->assertStringEndsNotWith('[prop1/inner1]', $result);
    }
}
