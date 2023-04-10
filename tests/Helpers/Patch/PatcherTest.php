<?php declare(strict_types=1);

namespace JsonWorks\Tests\Helpers\Patch;

use JohnStevenson\JsonWorks\Helpers\Patcher;

class PatcherTest extends \JsonWorks\Tests\Base
{
    protected Patcher $patcher;

    protected function setUp(): void
    {
        $this->patcher = new Patcher();
    }

    public function testAddEmptyArrayKey(): void
    {
        $data = json_decode('{
            "prop1": {
                "": [0, 1, 2, 3]
            }
        }');

        $expected = json_decode('{
            "prop1": {
                "": [0, 1, true, 2, 3]
            }
        }');

        $path = '/prop1//2';
        $value = true;

        self::assertTrue($this->patcher->add($data, $path, $value));
        self::assertEquals($expected, $data);
    }

    public function testReplaceNullRoot(): void
    {
        $data = null;

        $expected = json_decode('{
            "prop1": {
                "inner1": true
            }
        }');

        $path = '';
        $value = json_decode('{
            "prop1": {
                "inner1": true
            }
        }');

        self::assertTrue($this->patcher->replace($data, $path, $value));
        self::assertEquals($expected, $data);
    }

    public function testReplaceArrayValue(): void
    {
        $data = json_decode('{
            "collection": [0, 1, 2, 3]
        }');

        $expected = json_decode('{
            "collection": [0, 1, 4, 3]
        }');

        $path = '/collection/2';
        $value = 4;

        self::assertTrue($this->patcher->replace($data, $path, $value));
        self::assertEquals($expected, $data);
    }

    public function testReplaceObjectProperty(): void
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected = json_decode('{
            "prop1": {
                "inner1": true
            }
        }');

        $path = '/prop1/inner1';
        $value = true;

        self::assertTrue($this->patcher->replace($data, $path, $value));
        self::assertEquals($expected, $data);
    }

    public function testReplaceRootWithScalar(): void
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected = true;

        $path = '';
        $value = true;

        self::assertTrue($this->patcher->replace($data, $path, $value));
        self::assertEquals($expected, $data);
    }

    public function testReplaceMissingObjectPropertyFails(): void
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected = $data;

        $path = '/prop1/inner2';
        $value = true;

        self::assertFalse($this->patcher->replace($data, $path, $value));
        self::assertEquals($expected, $data);
    }

    /**
     * @dataProvider scalarProvider
     * @param mixed $data
     */
    public function testAssignToScalarFails($data, string $path): void
    {
        $expected = $data;
        self::assertFalse($this->patcher->add($data, $path, 'value'));
        self::assertEquals($expected, $data);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function scalarProvider(): array
    {
        $prop = '/prop1';
        $offset = '/0';

        return [
            'string with prop' =>   ['hello', $prop],
            'string with offset' => ['hello', $offset],
            'int with prop' =>      [10, $prop],
            'int with offset' =>    [10, $offset],
            'bool with prop' =>     [false, $prop],
            'bool with offset' =>   [false, $offset],
        ];
    }
}
