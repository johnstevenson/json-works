<?php

namespace JsonWorks\Tests\Helpers\Finder;

use JohnStevenson\JsonWorks\Helpers\Finder;

class ArrayKeyTest extends \JsonWorks\Tests\Base
{
    protected $finder;

    protected function setUp()
    {
        $this->finder = new Finder();
    }

    public function testIsArrayKeyTrue()
    {
        $values = ['0', '109'];
        $index = null;

        foreach ($values as $value) {
            $expected = (int) $value;
            $msg = sprintf('Testing key "%s"', $value);

            $result = $this->callMethod($this->finder, 'isArrayKey', [$value, &$index]);
            $this->assertTrue($result, $msg);
            $this->assertEquals($expected, $index, $msg);
        }
    }

    public function testIsArrayKeyFalse()
    {
        $values = ['-', '0009', '-8', 'prop1'];
        $index = null;

        foreach ($values as $value) {
            $msg = sprintf('Testing key "%s"', $value);

            $result = $this->callMethod($this->finder, 'isArrayKey', [$value, &$index]);
            $this->assertFalse($result, $msg);
        }
    }
}
