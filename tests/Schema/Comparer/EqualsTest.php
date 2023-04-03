<?php declare(strict_types=1);

namespace JsonWorks\Tests\Schema\Comparer;

use JohnStevenson\JsonWorks\Schema\Comparer;

class EqualsTest extends \PHPUnit\Framework\TestCase
{
    protected Comparer $comparer;

    protected function setUp(): void
    {
        $this->comparer = new Comparer();
    }

    public function testStringTrue(): void
    {
        $var1 = 'test string';
        $var2 = $var1;
        self::assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testStringFalse1(): void
    {
        $var1 = 'test string';
        $var2 = 'test string1';
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testStringFalse2(): void
    {
        $var1 = 'test string';
        $var2 = 8;
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testBooleanTrue(): void
    {
        $var1 = true;
        $var2 = $var1;
        self::assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testBooleanFalse1(): void
    {
        $var1 = true;
        $var2 = false;
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testBooleanFalse2(): void
    {
        $var1 = true;
        $var2 = 'test';
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testNullTrue(): void
    {
        $var1 = null;
        $var2 = $var1;
        self::assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testNullFalse1(): void
    {
        $var1 = null;
        $var2 = false;
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testNullFalse2(): void
    {
        $var1 = null;
        $var2 = 'test';
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testIntegerTrue(): void
    {
        $var1 = 654;
        $var2 = $var1;
        self::assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testIntegerFalse1(): void
    {
        $var1 = 654;
        $var2 = 655;
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testIntegerFalse2(): void
    {
        $var1 = 654;
        $var2 = array();
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testNumberTrue(): void
    {
        $var1 = 6.6;
        $var2 = $var1;
        self::assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testNumberFalse1(): void
    {
        $var1 = 6.6;
        $var2 = 6.5;
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testNumberFalse2(): void
    {
        $var1 = 6.6;
        $var2 = null;
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testMixedNumberTrue1(): void
    {
        $var1 = 6.0;
        $var2 = 6;
        self::assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testMixedNumberTrue2(): void
    {
        $var1 = 6;
        $var2 = 6.0;
        self::assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testMixedNumberFalse(): void
    {
        $var1 = 6.0;
        $var2 = 5;
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testArrayTrue(): void
    {
        $var1 = [1, true, 20];
        $var2 = $var1;
        self::assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testArrayFalse(): void
    {
        $var1 = [1, true, 20];
        $var2 = 9;
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testArrayFalseDifferentSize(): void
    {
        $var1 = [1, true, 20];
        $var2 = [1, true, 20, 5];
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testArrayFalseDifferentIndex(): void
    {
        $var1 = [1, true, 20];
        $var2 = [1, 20, true];
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testObjectTrue(): void
    {
        $var1 = (object) ['name' => 'name', 'other' => 'other'];
        $var2 = (object) ['other' => 'other', 'name' => 'name'];
        self::assertTrue($this->comparer->equals($var1, $var2));
    }

    public function testObjectFalse(): void
    {
        $var1 = (object) ['name' => 'name', 'other' => 'other'];
        $var2 = 'other';
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testObjectFalseDifferentSize(): void
    {
        $var1 = (object) ['name' => 'name', 'other' => 'other'];
        $var2 = (object) ['name' => 'name'];
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testObjectFalseDifferentKeys(): void
    {
        $var1 = (object) ['name' => 'name', 'other' => 'other'];
        $var2 = (object) ['other' => 'other', 'name1' => 'name1'];
        self::assertFalse($this->comparer->equals($var1, $var2));
    }

    public function testObjectFalseDifferentValues(): void
    {
        $var1 = (object) ['name' => 'name', 'other' => 'other'];
        $var2 = (object) ['other' => true, 'name' => 'name'];
        self::assertFalse($this->comparer->equals($var1, $var2));
    }
}
