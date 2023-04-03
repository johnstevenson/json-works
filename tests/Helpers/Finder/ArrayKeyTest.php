<?php declare(strict_types=1);

namespace JsonWorks\Tests\Helpers\Finder;

use JohnStevenson\JsonWorks\Helpers\Finder;

class ArrayKeyTest extends \JsonWorks\Tests\Base
{
    protected Finder $finder;

    protected function setUp(): void
    {
        $this->finder = new Finder();
    }

    public function testIsArrayKeyTrue(): void
    {
        $values = ['0', '109'];
        $index = null;

        foreach ($values as $value) {
            $expected = (int) $value;
            $msg = sprintf('Testing key "%s"', $value);

            $result = $this->callMethod($this->finder, 'isArrayKey', [$value, &$index]);
            self::assertTrue($result, $msg);
            self::assertEquals($expected, $index, $msg);
        }
    }

    public function testIsArrayKeyFalse(): void
    {
        $values = ['-', '0009', '-8', 'prop1'];
        $index = null;

        foreach ($values as $value) {
            $msg = sprintf('Testing key "%s"', $value);

            $result = $this->callMethod($this->finder, 'isArrayKey', [$value, &$index]);
            self::assertFalse($result, $msg);
        }
    }
}
