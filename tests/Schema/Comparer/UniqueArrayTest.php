<?php declare(strict_types=1);

namespace JsonWorks\Tests\Schema\Comparer;

use JohnStevenson\JsonWorks\Schema\Comparer;

class UniqueArrayTest extends \PHPUnit\Framework\TestCase
{
    protected Comparer $comparer;

    protected function setUp(): void
    {
        $this->comparer = new Comparer();
    }

    public function testCheckWithScalarTrue(): void
    {
        $value = [1, 'str', false];
        self::assertTrue($this->comparer->uniqueArray($value));
    }

    public function testCheckWithScalarFalse(): void
    {
        $value = [1, 'str', 'str', false];
        self::assertFalse($this->comparer->uniqueArray($value));
    }

    public function testCheckWithArrayTrue(): void
    {
        $arr1 = ['value'];
        $arr2 = ['value2'];

        $value = [1, 'str', $arr1, false, $arr2];
        self::assertTrue($this->comparer->uniqueArray($value));
    }

    public function testCheckWithArrayFalse(): void
    {
        $arr = ['value'];

        $value = [1, 'str', $arr, false, $arr];
        self::assertFalse($this->comparer->uniqueArray($value));
    }

    public function testCheckWithObjectTrue(): void
    {
        $obj1 = (object) ['name' => 'name', 'other' => 'other'];
        $obj2 = (object) ['other' => 'other', 'name' => 'value'];

        $value = [1, 'str', $obj1, false, $obj2];
        self::assertTrue($this->comparer->uniqueArray($value));
    }

    public function testCheckWithObjectFalse(): void
    {
        $obj1 = (object) ['name' => 'name', 'other' => 'other'];
        $obj2 = (object) ['other' => 'other', 'name' => 'name'];

        $value = [1, 'str', $obj1, false, $obj2];
        self::assertFalse($this->comparer->uniqueArray($value));
    }
}
