<?php declare(strict_types=1);

namespace JsonWorks\Tests\Schema;

use JohnStevenson\JsonWorks\Schema\JsonTypes;

class JsonTypesTest extends \JsonWorks\Tests\Base
{
    protected JsonTypes $jsonTypes;

    protected function setUp(): void
    {
        $this->jsonTypes = new JsonTypes;
    }

    public function testGenericWithInteger(): void
    {
        $value = 23;
        $expected = 'number';

        $type = $this->jsonTypes->getGeneric($value);
        self::assertEquals($expected, $type);
    }

    public function testGenericWithFloat(): void
    {
        $value = 23.7;
        $expected = 'number';

        $type = $this->jsonTypes->getGeneric($value);
        self::assertEquals($expected, $type);
    }

    public function testArray(): void
    {
        $value = [1, 2, 3];
        $type = 'array';

        $result = $this->jsonTypes->checkType($value, $type);
        self::assertTrue($result);
    }

    public function testObject(): void
    {
        $value = (object) ['name' => 'value'];
        $type = 'object';

        $result = $this->jsonTypes->checkType($value, $type);
        self::assertTrue($result);
    }

    public function testNull(): void
    {
        $value = null;
        $type = 'null';

        $result = $this->jsonTypes->checkType($value, $type);
        self::assertTrue($result);
    }

    public function testBoolean(): void
    {
        $value = false;
        $type = 'boolean';

        $result = $this->jsonTypes->checkType($value, $type);
        self::assertTrue($result);
    }

    public function testString(): void
    {
        $value = 'test string';
        $type = 'string';

        $result = $this->jsonTypes->checkType($value, $type);
        self::assertTrue($result);
    }

    public function testIntegerAsInteger(): void
    {
        $value = 21;
        $type = 'integer';

        $result = $this->jsonTypes->checkType($value, $type);
        self::assertTrue($result);
    }

    public function testIntegerLargeAsDouble(): void
    {
        $value = PHP_INT_MAX + 100;
        $type = 'integer';

        $result = $this->jsonTypes->checkType($value, $type);
        self::assertTrue($result);
    }

    public function testIntegerLargeAsDoubleNegative(): void
    {
        $value = ~PHP_INT_MAX - 100;
        $type = 'integer';

        $result = $this->jsonTypes->checkType($value, $type);
        self::assertTrue($result);
    }

    public function testIntegerAsNumber(): void
    {
        $value = 21;
        $type = 'number';

        $result = $this->jsonTypes->checkType($value, $type);
        self::assertTrue($result);
    }

    public function testFloatAsNumber(): void
    {
        $value = 21.2;
        $type = 'number';

        $result = $this->jsonTypes->checkType($value, $type);
        self::assertTrue($result);
    }

    public function testFloatNotInteger(): void
    {
        $value = 21.2;
        $type = 'integer';

        $result = $this->jsonTypes->checkType($value, $type);
        self::assertFalse($result);
    }

    public function testFloatCouldBeInteger(): void
    {
        $value = 21.0;
        $type = 'integer';

        $result = $this->jsonTypes->checkType($value, $type);
        self::assertFalse($result);
    }

    public function testUnknown(): void
    {
        $value = [1, 2, 3];
        $type = 'unknown';

        $result = $this->jsonTypes->checkType($value, $type);
        self::assertFalse($result);
    }

    public function testFail(): void
    {
        $value = [1, 2, 3];
        $type = 'object';

        $result = $this->jsonTypes->checkType($value, $type);
        self::assertFalse($result);
    }

    public function testArrayOfInteger(): void
    {
        $value = [100, -3, 4];
        $type = 'integer';

        $result = $this->jsonTypes->arrayOfType($value, $type);
        self::assertTrue($result, 'Testing success');

        $value = [100, -3, 4.78];
        $result = $this->jsonTypes->arrayOfType($value, $type);
        self::assertFalse($result, 'Testing failure');
    }

    public function testArrayOfNumber(): void
    {
        $value = [100, -3, 4.78];
        $type = 'number';

        $result = $this->jsonTypes->arrayOfType($value, $type);
        self::assertTrue($result, 'Testing success');

        $value = [100, null, 4.78];
        $result = $this->jsonTypes->arrayOfType($value, $type);
        self::assertFalse($result, 'Testing failure');
    }

    public function testArrayOfString(): void
    {
        $value = ['one', 'two', 'three'];
        $type = 'string';

        $result = $this->jsonTypes->arrayOfType($value, $type);
        self::assertTrue($result, 'Testing success');

        $value = ['one', true, 'three'];
        $result = $this->jsonTypes->arrayOfType($value, $type);
        self::assertFalse($result, 'Testing failure');
    }

    public function testArrayOfObject(): void
    {
        $object = new \stdClass();
        $value = [$object, $object, $object];
        $type = 'object';

        $result = $this->jsonTypes->arrayOfType($value, $type);
        self::assertTrue($result, 'Testing success');

        $value = [$object, [], $object];
        $result = $this->jsonTypes->arrayOfType($value, $type);
        self::assertFalse($result, 'Testing failure');
    }
}
