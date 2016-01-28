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

        $result = $this->finder->get($data, $target);
        $this->assertTrue($result);
        $this->assertTrue($this->sameRef($expected, $target->element));
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
        $result = $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        $this->assertTrue($result, $msg);
        $this->assertTrue($this->sameRef($expected, $target->element), $msg);
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
        $result = $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        $this->assertTrue($result, $msg);
        $this->assertTrue($this->sameRef($expected, $target->element), $msg);
        $this->assertEquals('', $error, $msg);

        // check parent
        $msg = 'Testing parent';
        $expected =& $data->prop1->inner1;
        $this->assertTrue($this->sameRef($expected, $target->parent), $msg);
        $this->assertEquals('2', $target->childKey, $msg);
    }

    public function testGetEmptyKey()
    {
        $data = json_decode('{
            "prop1": {
                "": [0, 1, 2, 3]
            }
        }');

        $expected =& $data->prop1->_empty_[1];

        $path = '/prop1//1';
        $target = new Target($path, $error);
        $result = $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        $this->assertTrue($result, $msg);
        $this->assertTrue($this->sameRef($expected, $target->element), $msg);
        $this->assertEquals('', $error, $msg);

        // check parent
        $msg = 'Testing parent';
        $expected =& $data->prop1->_empty_;
        $this->assertTrue($this->sameRef($expected, $target->parent), $msg);
        $this->assertEquals('1', $target->childKey, $msg);
    }

    public function testGetNotFound()
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected =& $data->prop1->inner1;

        $path = '/prop1/inner1/8';
        $target = new Target($path, $error);
        $result = $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        $this->assertFalse($result, $msg);
        $this->assertTrue($this->sameRef($expected, $target->element), $msg);
        $this->assertContains('ERR_NOT_FOUND', $target->error, $msg);

        // check parent, will be the same as element
        $msg = 'Testing parent';
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

        $expected =& $data->prop1->inner1;

        $path = '/prop1/inner1/item';
        $target = new Target($path, $error);
        $result = $this->finder->get($data, $target);

        // check result
        $msg = 'Testing result';
        $this->assertFalse($result, $msg);
        $this->assertTrue($this->sameRef($expected, $target->element), $msg);
        $this->assertContains('ERR_PATH_KEY', $target->error, $msg);
        $this->assertTrue($target->invalid);

        // check parent, will be the same as element
        $msg = 'Testing parent';
        $this->assertTrue($this->sameRef($expected, $target->parent), $msg);
        $this->assertEquals('item', $target->childKey, $msg);
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
        $result = $this->finder->find($path, $data, $element, $error);

        // check result
        $this->assertTrue($result);
        $this->assertEquals($expected, $element);
        $this->assertEmpty($error);

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
        $result = $this->finder->find($path, $data, $element, $error);

        // check result
        $this->assertFalse($result);
        $this->assertContains('ERR_NOT_FOUND', $error);

        // check passed-in $element has not been modified
        $this->assertEquals($expected, $element);
    }

    public function testFindInvalidKey()
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $element = 'original-value';
        $expected = $element;

        $path = '/prop1/inner1/item';
        $result = $this->finder->find($path, $data, $element, $error);

        // check result
        $this->assertFalse($result);
        $this->assertContains('ERR_PATH_KEY', $error);

        // check passed-in $element has not been modified
        $this->assertEquals($expected, $element);
    }

    public function testFindEmptyKey()
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $element = 'original-value';
        $expected = $element;

        $path = '/prop1/inner1//item';
        $result = $this->finder->find($path, $data, $element, $error);

        // check result
        $this->assertFalse($result);
        $this->assertContains('ERR_PATH_KEY', $error);

        // check passed-in $element has not been modified
        $this->assertEquals($expected, $element);
    }
}
