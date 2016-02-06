<?php

namespace JsonWorks\Tests\Schema\Comparer;

use JohnStevenson\JsonWorks\Schema\Comparer;

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
}
