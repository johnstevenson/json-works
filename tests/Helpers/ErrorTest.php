<?php declare(strict_types=1);

namespace JsonWorks\Tests\Helpers;

use JohnStevenson\JsonWorks\Helpers\Error;

class ErrorTest extends \PHPUnit\Framework\TestCase
{
    protected Error $error;

    protected function setUp(): void
    {
        $this->error = new Error();
    }

    public function testKnownCodes(): void
    {
        $class = new \ReflectionClass($this->error);
        $msg = 'something';
        $expected = '[something]';

        foreach ($class->getConstants() as $code) {
            $result = $this->error->get($code, $msg);
            self::assertStringStartsWith($code, $result);
            $expected = $code !== Error::ERR_VALIDATE ? '[something]' : $msg;
            self::assertStringEndsWith($expected, $result);
        }
    }

    public function testUnknownCode(): void
    {
        $code = 'ERR_UNKNOWN';
        $msg = 'Custom message';

        $result = $this->error->get($code, $msg);
        self::assertStringStartsWith('ERR_UNKNOWN', $result);
        self::assertStringEndsWith('Custom message', $result);
    }
}
