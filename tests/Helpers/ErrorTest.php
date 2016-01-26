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

    public function testKnownCodes()
    {
        $class = new \ReflectionClass($this->error);
        $msg = 'something';
        $expected = '[something]';

        foreach ($class->getConstants() as $code) {
            $result = $this->error->get($code, $msg);
            $this->assertStringStartsWith($code, $result);
            $expected = $code !== Error::ERR_VALIDATE ? '[something]' : $msg;
            $this->assertStringEndsWith($expected, $result);
        }
    }

    public function testUnknownCode()
    {
        $code = 'ERR_UNKNOWN';
        $msg = 'Custom message';

        $result = $this->error->get($code, $msg);
        $this->assertStringStartsWith('ERR_UNKNOWN', $result);
        $this->assertStringEndsWith('Custom message', $result);
    }
}
