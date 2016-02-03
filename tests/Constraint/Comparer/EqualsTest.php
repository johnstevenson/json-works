<?php

namespace JsonWorks\Tests\Constraint\Comparer;

use JohnStevenson\JsonWorks\Schema\Constraints\Comparer;

class EqualsTest extends \PHPUnit_Framework_TestCase
{
    protected $comparer;

    protected function setUp()
    {
        $this->comparer = new Comparer();
    }

    public function testStringTrue()
    {
        $var1 = 'test string';
        $var2 = $var1;
        $this->assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testStringFalse1()
    {
        $var1 = 'test string';
        $var2 = 'test string1';
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testStringFalse2()
    {
        $var1 = 'test string';
        $var2 = 8;
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testBooleanTrue()
    {
        $var1 = true;
        $var2 = $var1;
        $this->assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testBooleanFalse1()
    {
        $var1 = true;
        $var2 = false;
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testBooleanFalse2()
    {
        $var1 = true;
        $var2 = 'test';
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testNullTrue()
    {
        $var1 = null;
        $var2 = $var1;
        $this->assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testNullFalse1()
    {
        $var1 = null;
        $var2 = false;
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testNullFalse2()
    {
        $var1 = null;
        $var2 = 'test';
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testIntegerTrue()
    {
        $var1 = 654;
        $var2 = $var1;
        $this->assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testIntegerFalse1()
    {
        $var1 = 654;
        $var2 = 655;
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testIntegerFalse2()
    {
        $var1 = 654;
        $var2 = array();
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testNumberTrue()
    {
        $var1 = 6.6;
        $var2 = $var1;
        $this->assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testNumberFalse1()
    {
        $var1 = 6.6;
        $var2 = 6.5;
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testNumberFalse2()
    {
        $var1 = 6.6;
        $var2 = null;
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testMixedNumberTrue1()
    {
        $var1 = 6.0;
        $var2 = 6;
        $this->assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testMixedNumberTrue2()
    {
        $var1 = 6;
        $var2 = 6.0;
        $this->assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testMixedNumberFalse()
    {
        $var1 = 6.0;
        $var2 = 5;
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testArrayTrue()
    {
        $var1 = [1, true, 20];
        $var2 = $var1;
        $this->assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testArrayFalse()
    {
        $var1 = [1, true, 20];
        $var2 = 9;
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testArrayFalseDifferentSize()
    {
        $var1 = [1, true, 20];
        $var2 = [1, true, 20, 5];
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testArrayFalseDifferentIndex()
    {
        $var1 = [1, true, 20];
        $var2 = [1, 20, true];
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testObjectTrue()
    {
        $var1 = (object) ['name' => 'name', 'other' => 'other'];
        $var2 = (object) ['other' => 'other', 'name' => 'name'];
        $this->assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testObjectFalse()
    {
        $var1 = (object) ['name' => 'name', 'other' => 'other'];
        $var2 = 'other';
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testObjectFalseDifferentSize()
    {
        $var1 = (object) ['name' => 'name', 'other' => 'other'];
        $var2 = (object) ['name' => 'name'];
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testObjectFalseDifferentKeys()
    {
        $var1 = (object) ['name' => 'name', 'other' => 'other'];
        $var2 = (object) ['other' => 'other', 'name1' => 'name1'];
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testObjectFalseDifferentValues()
    {
        $var1 = (object) ['name' => 'name', 'other' => 'other'];
        $var2 = (object) ['other' => true, 'name' => 'name'];
        $this->assertFalse($this->comparer->equals($var1, $var2));
    }
}
