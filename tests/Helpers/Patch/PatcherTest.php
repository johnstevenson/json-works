<?php

namespace JsonWorks\Tests\Helpers\Patch;

use JohnStevenson\JsonWorks\Helpers\Patcher;

class PatcherTest extends \JsonWorks\Tests\Base
{
    protected $patcher;

    protected function setUp()
    {
        $this->patcher = new Patcher();
    }

    public function testAddEmptyArrayKey()
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

        $this->assertTrue($this->patcher->add($data, $path, $value));
        $this->assertEquals($expected, $data);
    }

    public function testReplaceNullRoot()
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

        $this->assertTrue($this->patcher->replace($data, $path, $value));
        $this->assertEquals($expected, $data);
    }

    public function testReplaceArrayValue()
    {
        $data = json_decode('{
            "collection": [0, 1, 2, 3]
        }');

        $expected = json_decode('{
            "collection": [0, 1, 4, 3]
        }');

        $path = '/collection/2';
        $value = 4;

        $this->assertTrue($this->patcher->replace($data, $path, $value));
        $this->assertEquals($expected, $data);
    }

    public function testReplaceObjectProperty()
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

        $this->assertTrue($this->patcher->replace($data, $path, $value));
        $this->assertEquals($expected, $data);
    }

    public function testReplaceRootWithScalar()
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected = true;

        $path = '';
        $value = true;

        $this->assertTrue($this->patcher->replace($data, $path, $value));
        $this->assertEquals($expected, $data);
    }

    public function testReplaceMissingObjectPropertyFails()
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected = $data;

        $path = '/prop1/inner2';
        $value = true;

        $this->assertFalse($this->patcher->replace($data, $path, $value));
        $this->assertEquals($expected, $data);
    }
}
