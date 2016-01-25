<?php

namespace JsonWorks\Tests\Helpers\Finder;

use JohnStevenson\JsonWorks\Helpers\Error;
use JohnStevenson\JsonWorks\Helpers\Finder;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;

class FinderTest extends \JsonWorks\Tests\Base
{
    protected $finder;

    protected function setUp()
    {
        $this->finder = new Finder();
    }

    public function testGetRoot()
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected =& $data;

        $path = '';
        $target = new Target($path, $error);

        $result =& $this->finder->get($data, $target);
        $this->assertTrue($target->found);
        $this->assertTrue($this->sameRef($expected, $result));
        $this->assertEquals('', $error);
    }

    public function testGetNestedObjectProperty()
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected =& $data->prop1->inner1;

        $path = '/prop1/inner1';
        $target = new Target($path, $error);
        $result =& $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        $this->assertTrue($target->found, $msg);
        $this->assertTrue($this->sameRef($expected, $result), $msg);
        $this->assertEquals('', $error, $msg);

        // check parent
        $msg = 'Testing parent';
        $expected =& $data->prop1;
        $this->assertTrue($this->sameRef($expected, $target->parent), $msg);
        $this->assertEquals('inner1', $target->childKey, $msg);
    }

    public function testGetNestedArrayItem()
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected =& $data->prop1->inner1[2];

        $path = '/prop1/inner1/2';
        $target = new Target($path, $error);
        $result =& $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        $this->assertTrue($target->found, $msg);
        $this->assertTrue($this->sameRef($expected, $result), $msg);
        $this->assertEquals('', $error, $msg);

        // check parent
        $msg = 'Testing parent';
        $expected =& $data->prop1->inner1;
        $this->assertTrue($this->sameRef($expected, $target->parent), $msg);
        $this->assertEquals('2', $target->childKey, $msg);
    }

    public function testGetNotFound()
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $path = '/prop1/inner1/8';
        $target = new Target($path, $error);
        $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        $this->assertFalse($target->found, $msg);
        $this->assertEquals(Error::ERR_NOT_FOUND, $target->errorCode, $msg);

        // check parent
        $msg = 'Testing parent';
        $expected =& $data->prop1->inner1;
        $this->assertTrue($this->sameRef($expected, $target->parent), $msg);
        $this->assertEquals('8', $target->childKey, $msg);
    }

    public function testGetInvalidKey()
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $path = '/prop1/inner1/item';
        $target = new Target($path, $error);
        $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        $this->assertFalse($target->found, $msg);
        $this->assertEquals(Error::ERR_KEY_INVALID, $target->errorCode, $msg);

        // check parent
        $msg = 'Testing parent';
        $expected =& $data->prop1->inner1;
        $this->assertTrue($this->sameRef($expected, $target->parent), $msg);
        $this->assertEquals('item', $target->childKey, $msg);
    }

    public function testGetEmptyKey()
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $path = '/prop1//item';
        $target = new Target($path, $error);
        $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        $this->assertFalse($target->found, $msg);
        $this->assertEquals(Error::ERR_KEY_EMPTY, $target->errorCode, $msg);

        // check parent
        $msg = 'Testing parent';
        $expected =& $data->prop1->inner1;
        $this->assertNull($target->parent, $msg);
        $this->assertEquals('', $target->childKey, $msg);
    }

    public function testFind()
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected = 2;

        $path = '/prop1/inner1/2';
        $result = $this->finder->find($path, $data, $element);

        // check result
        $this->assertTrue($result);
        $this->assertEquals($expected, $element);

        // check $element does not reference the data
        $dataRef =& $data->prop1->inner1[2];
        $this->assertFalse($this->sameRef($dataRef, $element));
    }

    public function testFindNotFound()
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $element = 'original-value';
        $expected = $element;

        $path = '/prop1/inner1/8';
        $result = $this->finder->find($path, $data, $element);

        // check result
        $this->assertFalse($result);

        // check passed-in $element has not been modified
        $this->assertEquals($expected, $element);
    }
}
