<?php

namespace JsonWorks\Tests\Utils;

use \JohnStevenson\JsonWorks\Utils;

class CheckTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testArray()
    {
        $value = array(1, 2, 3);
        $this->assertTrue(Utils::checkType('array', $value));
    }

    public function testObject()
    {
        $value = (object) array('name' => 'value');
        $this->assertTrue(Utils::checkType('object', $value));
    }

    public function testNull()
    {
        $value = null;
        $this->assertTrue(Utils::checkType('null', $value));
    }

    public function testBoolean()
    {
        $value = false;
        $this->assertTrue(Utils::checkType('boolean', $value));
    }

    public function testString()
    {
        $value = 'test string';
        $this->assertTrue(Utils::checkType('string', $value));
    }

    public function testIntegerAsInteger()
    {
        $value = 21;
        $this->assertTrue(Utils::checkType('integer', $value));
    }

    public function testIntegerArbitrarilyLarge() {
        $value = PHP_INT_MAX + 100;
        $this->assertTrue(Utils::checkType('integer', $value));
    }

    public function testIntegerAsNumber()
    {
        $value = 21;
        $this->assertTrue(Utils::checkType('number', $value));
    }

    public function testFloatAsNumber()
    {
        $value = 21.2;
        $this->assertTrue(Utils::checkType('number', $value));
    }

    public function testFloatNotInteger()
    {
        $value = 21.2;
        $this->assertFalse(Utils::checkType('integer', $value));
    }

    public function testUnknown()
    {
        $value = array(1, 2, 3);
        $this->assertFalse(Utils::checkType('unknown', $value));
    }

    public function testFail()
    {
        $value = array(1, 2, 3);
        $this->assertFalse(Utils::checkType('object', $value));
    }
}

