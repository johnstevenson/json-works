<?php

namespace JsonWorks\Tests\Utils;

use \JohnStevenson\JsonWorks\Utils;

class UniqueArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testCheckWithScalarTrue()
    {
        $value = array(1, 'str', false);
        $this->assertTrue(Utils::uniqueArray($value, true));
    }

    public function testCheckWithScalarFalse()
    {
        $value = array(1, 'str', 'str', false);
        $this->assertFalse(Utils::uniqueArray($value, true));
    }

    public function testCheckWithArrayTrue()
    {
        $arr1 = array('value');
        $arr2 = array('value2');

        $value = array(1, 'str', $arr1, false, $arr2);
        $this->assertTrue(Utils::uniqueArray($value, true));
    }

    public function testCheckWithArrayFalse()
    {
        $arr = array('value');

        $value = array(1, 'str', $arr, false, $arr);
        $this->assertFalse(Utils::uniqueArray($value, true));
    }

    public function testCheckWithObjectTrue()
    {
        $obj1 = (object) array('name' => 'name', 'other' => 'other');
        $obj2 = (object) array('other' => 'other', 'name' => 'value');

        $value = array(1, 'str', $obj1, false, $obj2);
        $this->assertTrue(Utils::uniqueArray($value, true));
    }

    public function testCheckWithObjectFalse()
    {
        $obj1 = (object) array('name' => 'name', 'other' => 'other');
        $obj2 = (object) array('other' => 'other', 'name' => 'name');

        $value = array(1, 'str', $obj1, false, $obj2);
        $this->assertFalse(Utils::uniqueArray($value, true));
    }

    public function testUniqueWithScalar()
    {
        $value = array(1,2,3,4,5,2,3,1);
        $expected = array(1,2,3,4,5);
        $this->assertEquals($expected, Utils::uniqueArray($value));
    }

    public function testUniqueWithObject()
    {
        $obj1 = (object) array('name' => 'name', 'other' => 'other');
        $obj2 = (object) array('other' => 'other', 'name' => 'name');

        $value = array(1, 'str', $obj1, false, $obj2, 2, 1);
        $expected = array(1, 'str', $obj1, false, 2);
        $this->assertEquals($expected, Utils::uniqueArray($value));
    }
}
