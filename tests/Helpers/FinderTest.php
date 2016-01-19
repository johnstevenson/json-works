<?php

namespace JsonWorks\Tests\Helpers;

use JohnStevenson\JsonWorks\Helpers\Finder;

class FinderTest extends \JsonWorks\Tests\Base
{
    protected $finder;

    protected function setUp()
    {
        $this->finder = new Finder();
    }

    protected function call($name, $args)
    {
        return $this->callMethod($this->finder, $name, $args);
    }

    public function testIsArrayKeyTrue()
    {
        $token = '0';
        $expected = (int) $token;

        $result = $this->finder->isArrayKey($token, $index);
        $this->assertTrue($result, 'token passes: '.$token);
        $this->assertEquals($expected, $index, 'index equals: '.$expected);

        $token = '109';
        $expected = (int) $token;

        $result = $this->finder->isArrayKey($token, $index);
        $this->assertTrue($result, 'token passes: '.$token);
        $this->assertEquals($expected, $index, 'index equals: '.$expected);
    }

    public function testArrayKeyFalse()
    {
        $token = '-';
        $result = $this->finder->isArrayKey($token, $index);
        $this->assertFalse($result, 'token fails: '.$token);

        $token = '0009';
        $result = $this->finder->isArrayKey($token, $index);
        $this->assertFalse($result, 'token fails: '.$token);

        $token = 'string';
        $result = $this->finder->isArrayKey($token, $index);
        $this->assertFalse($result, 'token fails: '.$token);
    }
}
