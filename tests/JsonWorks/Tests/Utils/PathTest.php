<?php

namespace JsonWorks\Tests\Utils;

use \JohnStevenson\JsonWorks\Utils as Utils;

class PathTest extends \PHPUnit_Framework_TestCase
{
    public function testAddToPath()
    {
        $path = '/key1';
        $value = 'key2';
        $expected = '/key1/key2';
        $this->assertEquals($expected, Utils::addToPath($path, $value));

    }

    public function testAddToPathEmpty()
    {
        $path = '';
        $value = 'key1';
        $expected = '/key1';
        $this->assertEquals($expected, Utils::addToPath($path, $value));

    }

    public function testAddToPathEmptyWithEmpty()
    {
        $path = '';
        $value = '';
        $expected = '';
        $this->assertEquals($expected, Utils::addToPath($path, $value));
    }

    public function testAddToPathEmptyWithZero()
    {
        $path = '';
        $value = '0';
        $expected = '/0';
        $this->assertEquals($expected, Utils::addToPath($path, $value));
    }

    public function testDecodePathPlain()
    {
        $value = '/key1/key2/key3';
        $expected = array('key1', 'key2', 'key3');
        $this->assertEquals($expected, Utils::decodePath($value));
    }

    public function testDecodePathEncoded()
    {
        $value = '/key~01/key~02~1sub';
        $expected = array('key~1', 'key~2/sub');
        $this->assertEquals($expected, Utils::decodePath($value));
    }

    public function testEncodePathPlain()
    {
        $value = 'key1';
        $expected = '/key1';
        $this->assertEquals($expected, Utils::encodePath($value));
    }

    public function testEncodePathWithEncodeChars()
    {
        $value = array('key~1', 'key~2//sub', 'key3');
        $expected = '/key~01/key~02~1~1sub/key3';
        $this->assertEquals($expected, Utils::encodePath($value));
    }

    public function testEncodePathEmpty()
    {
        $value = '';
        $expected = '';
        $this->assertEquals($expected, Utils::encodePath($value));
    }

    public function testEncodePathWithZero()
    {
        $value = '0';
        $expected = '/0';
        $this->assertEquals($expected, Utils::encodePath($value));
    }
}
