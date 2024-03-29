<?php declare(strict_types=1);

namespace JsonWorks\Tests\Helpers\Finder;

use JohnStevenson\JsonWorks\Helpers\Error;
use JohnStevenson\JsonWorks\Helpers\Finder;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;

class FinderTest extends \JsonWorks\Tests\Base
{
    protected Finder $finder;

    protected function setUp(): void
    {
        $this->finder = new Finder();
    }

    public function testGetRootObject(): void
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected =& $data;

        $path = '';
        $error = '';
        $target = new Target($path, $error);

        $result = $this->finder->get($data, $target);
        self::assertTrue($result);
        self::assertTrue($this->sameRef($expected, $target->element));
        self::assertEquals('', $error);
    }

    public function testGetRootArray(): void
    {
        $data = json_decode('[0, 1, 2, 3]');

        $expected =& $data;

        $path = '';
        $error = '';
        $target = new Target($path, $error);

        $result = $this->finder->get($data, $target);
        self::assertTrue($result);
        self::assertTrue($this->sameRef($expected, $target->element));
        self::assertEquals('', $error);
    }

    public function testGetRootArrayValue(): void
    {
        $data = json_decode('[0, 1, 2, 3]');

        $expected =& $data[2];

        $path = '/2';
        $error = '';
        $target = new Target($path, $error);

        $result = $this->finder->get($data, $target);
        self::assertTrue($result);
        self::assertTrue($this->sameRef($expected, $target->element));
        self::assertEquals('', $error);
    }

    public function testGetNestedObjectProperty(): void
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected =& $data->prop1->inner1;

        $path = '/prop1/inner1';
        $error = '';
        $target = new Target($path, $error);
        $result = $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        self::assertTrue($result, $msg);
        self::assertTrue($this->sameRef($expected, $target->element), $msg);
        self::assertEquals('', $error, $msg);

        // check parent
        $msg = 'Testing parent';
        $expected =& $data->prop1;
        self::assertTrue($this->sameRef($expected, $target->parent), $msg);
        self::assertEquals('inner1', $target->childKey, $msg);
    }

    public function testGetNestedArrayItem(): void
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected =& $data->prop1->inner1[2];

        $path = '/prop1/inner1/2';
        $error = '';
        $target = new Target($path, $error);
        $result = $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        self::assertTrue($result, $msg);
        self::assertTrue($this->sameRef($expected, $target->element), $msg);
        self::assertEquals('', $error, $msg);

        // check parent
        $msg = 'Testing parent';
        $expected =& $data->prop1->inner1;
        self::assertTrue($this->sameRef($expected, $target->parent), $msg);
        self::assertEquals('2', $target->childKey, $msg);
    }

    public function testGetEmptyKey(): void
    {
        $data = json_decode('{
            "prop1": {
                "": [0, 1, 2, 3]
            }
        }');

        $empty = '';
        // @phpstan-ignore-next-line
        $expected =& $data->prop1->$empty[1];

        $path = '/prop1//1';
        $error = '';
        $target = new Target($path, $error);
        $result = $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        self::assertTrue($result, $msg);
        self::assertTrue($this->sameRef($expected, $target->element), $msg);
        self::assertEquals('', $error, $msg);

        // check parent
        $msg = 'Testing parent';
        // @phpstan-ignore-next-line
        $expected =& $data->prop1->$empty;
        self::assertTrue($this->sameRef($expected, $target->parent), $msg);
        self::assertEquals('1', $target->childKey, $msg);
    }

    public function testGetNotFound(): void
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected =& $data->prop1->inner1;

        $path = '/prop1/inner1/8';
        $error = '';
        $target = new Target($path, $error);
        $result = $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        self::assertFalse($result, $msg);
        self::assertTrue($this->sameRef($expected, $target->element), $msg);
        self::assertStringContainsString('ERR_NOT_FOUND', $target->error, $msg);

        // check parent, will be the same as element
        $msg = 'Testing parent';
        self::assertTrue($this->sameRef($expected, $target->parent), $msg);
        self::assertEquals('8', $target->childKey, $msg);
    }

    public function testGetInvalidKey(): void
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected =& $data->prop1->inner1;

        $path = '/prop1/inner1/item';
        $error = '';
        $target = new Target($path, $error);
        $result = $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        self::assertFalse($result, $msg);
        self::assertTrue($this->sameRef($expected, $target->element), $msg);
        self::assertStringContainsString('ERR_PATH_KEY', $target->error, $msg);
        self::assertTrue($target->invalid);

        // check parent, will be the same as element
        $msg = 'Testing parent';
        self::assertTrue($this->sameRef($expected, $target->parent), $msg);
        self::assertEquals('item', $target->childKey, $msg);
    }

    public function testGetChecksForInvalidTarget(): void
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected =& $data;

        $path = 'prop1/inner1/2';
        $error = '';
        $target = new Target($path, $error);
        $result = $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        self::assertFalse($result, $msg);
        self::assertNull($target->element, $msg);

        // check parent is null
        $msg = 'Testing parent';
        self::assertNull($target->parent, $msg);
    }

    public function testFind(): void
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected = 2;

        $path = '/prop1/inner1/2';
        $error = '';
        $result = $this->finder->find($path, $data, $element, $error);

        // check result
        self::assertTrue($result);
        self::assertEquals($expected, $element);
        self::assertEmpty($error);

        // check $element does not reference the data
        $dataRef =& $data->prop1->inner1[2];
        self::assertFalse($this->sameRef($dataRef, $element));
    }

    public function testFindNotFound(): void
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $element = 'original-value';
        $expected = $element;

        $path = '/prop1/inner1/8';
        $error = '';
        $result = $this->finder->find($path, $data, $element, $error);

        // check result
        self::assertFalse($result);
        self::assertStringContainsString('ERR_NOT_FOUND', $error);

        // check passed-in $element has not been modified
        self::assertEquals($expected, $element);
    }

    public function testFindInvalidKey(): void
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $element = 'original-value';
        $expected = $element;

        $path = '/prop1/inner1/item';
        $error = '';
        $result = $this->finder->find($path, $data, $element, $error);

        // check result
        self::assertFalse($result);
        self::assertStringContainsString('ERR_PATH_KEY', $error);

        // check passed-in $element has not been modified
        self::assertEquals($expected, $element);
    }

    public function testFindEmptyKey(): void
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $element = 'original-value';
        $expected = $element;

        $path = '/prop1/inner1//item';
        $error = '';
        $result = $this->finder->find($path, $data, $element, $error);

        // check result
        self::assertFalse($result);
        self::assertStringContainsString('ERR_PATH_KEY', $error);

        // check passed-in $element has not been modified
        self::assertEquals($expected, $element);
    }

    /**
    * Tests the example given in RFC6901
    *
    * https://tools.ietf.org/html/rfc6901    *
    */
    public function testRfc6901(): void
    {
        $json = '{
            "foo": ["bar", "baz"],
            "": 0,
            "a/b": 1,
            "c%d": 2,
            "e^f": 3,
            "g|h": 4,
            "i\\\\j": 5,
            "k\\"l": 6,
            " ": 7,
            "m~n": 8
        }';

        $data = $this->objectFromJson($json);

        $tests = [
            "" => $data,
            "/foo"      => ["bar", "baz"],
            "/foo/0"    => "bar",
            "/"         => 0,
            "/a~1b"     => 1,
            "/c%d"      => 2,
            "/e^f"      => 3,
            "/g|h"      => 4,
            "/i\\j"     => 5,
            "/k\"l"     => 6,
            "/ "        => 7,
            "/m~0n"     => 8,
        ];

        foreach ($tests as $pointer => $expected) {
            $msg = 'Testing ' . ($pointer === '' ?  'empty key' : "'{$pointer}'" );
            $error = '';
            $result = $this->finder->find($pointer, $data, $element, $error);
            self::assertTrue($result, $msg);
            self::assertEquals($expected, $element, $msg);
        }
    }
}
