<?php

namespace JsonWorks\Tests\Constraint\Comparer;

use JohnStevenson\JsonWorks\Schema\Constraints\Comparer;

class UniqueArrayTest extends \PHPUnit_Framework_TestCase
{
    protected $comparer;

    protected function setUp()
    {
        $this->comparer = new Comparer();
    }

    public function testCheckWithScalarTrue()
    {
        $value = [1, 'str', false];
        $this->assertTrue($this->comparer->uniqueArray($value));
    }

    public function testCheckWithScalarFalse()
    {
        $value = [1, 'str', 'str', false];
        $this->assertFalse($this->comparer->uniqueArray($value));
    }

    public function testCheckWithArrayTrue()
    {
        $arr1 = ['value'];
        $arr2 = ['value2'];

        $value = [1, 'str', $arr1, false, $arr2];
        $this->assertTrue($this->comparer->uniqueArray($value));
    }

    public function testCheckWithArrayFalse()
    {
        $arr = ['value'];

        $value = [1, 'str', $arr, false, $arr];
        $this->assertFalse($this->comparer->uniqueArray($value));
    }

    public function testCheckWithObjectTrue()
    {
        $obj1 = (object) ['name' => 'name', 'other' => 'other'];
        $obj2 = (object) ['other' => 'other', 'name' => 'value'];

        $value = [1, 'str', $obj1, false, $obj2];
        $this->assertTrue($this->comparer->uniqueArray($value));
    }

    public function testCheckWithObjectFalse()
    {
        $obj1 = (object) ['name' => 'name', 'other' => 'other'];
        $obj2 = (object) ['other' => 'other', 'name' => 'name'];

        $value = [1, 'str', $obj1, false, $obj2];
        $this->assertFalse($this->comparer->uniqueArray($value));
    }

    public function testArrayOfStringTrue()
    {
        $value = ['one', 'two', 'three', 'four', 'five'];
        $this->assertTrue($this->comparer->uniqueArrayOfString($value));
    }

    public function testArrayOfStringFalse1()
    {
        $value = ['one', 'two', 'two', 'four', 'five'];
        $this->assertFalse($this->comparer->uniqueArrayOfString($value));
    }

    public function testArrayOfStringFalse2()
    {
        $value = ['one', 'two', 2, 'four', 'five'];
        $this->assertFalse($this->comparer->uniqueArrayOfString($value));
    }
}
