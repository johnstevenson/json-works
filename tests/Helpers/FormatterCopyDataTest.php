<?php

namespace JsonWorks\Tests\Helpers;

use JohnStevenson\JsonWorks\Helpers\Formatter;

class FormatterCopyDataTest extends \JsonWorks\Tests\Base
{
    protected $formatter;

    protected function setUp()
    {
        $this->formatter = new Formatter();
    }

    public function testFromObject()
    {
        $obj1 = (object) array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj2 = $this->formatter->copyData($obj1);

        $obj1->lastName = 'Smith';
        $expected = 'Bloggs';
        $this->assertEquals($expected, $obj2->lastName);
    }

    public function testFromAssoc()
    {
        $arr = array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj = $this->formatter->copyData($arr);

        $expected = (object) $arr;
        $this->assertEquals($expected, $obj);
    }

    public function testDeepFromObject()
    {
        $obj = (object) array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj1 = (object) array('users' => array($obj));
        $obj2 = $this->formatter->copyData($obj1);

        $obj1->users[0]->lastName = 'Smith';
        $expected = 'Bloggs';
        $this->assertEquals($expected, $obj2->users[0]->lastName);
    }

    public function testDeepFromAssoc()
    {
        $arr = array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj1 = (object) array('users' => array($arr));

        $obj2 = $this->formatter->copyData($obj1);

        $expected = (object) $arr;
        $this->assertEquals($expected, $obj2->users[0]);
    }

    public function testArrayDeepFromObject()
    {
        $obj = (object) array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj1 = (object) array('users' => array($obj));
        $arr1 = array(9, $obj1);

        $arr2 = $this->formatter->copyData($arr1);

        $arr1[1]->users[0]->lastName = 'Smith';
        $expected = 'Bloggs';
        $this->assertEquals($expected, $arr2[1]->users[0]->lastName);
    }

    public function testArrayDeepFromAssoc()
    {
        $arr = array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj = (object) array('users' => array($arr));
        $arr1 = array(9, $obj);

        $arr2 = $this->formatter->copyData($arr1);

        $expected = (object) $arr;
        $this->assertEquals($expected, $arr2[1]->users[0]);
    }

    public function testObjectFromEmptyObject()
    {
        $obj = new \stdClass();
        $result = $this->formatter->copyData($obj);

        $expected =  new \stdClass();
        $this->assertEquals($expected, $result);
    }

    public function testArrayFromEmptyArray()
    {
        $arr = array();
        $result = $this->formatter->copyData($arr);
        $expected =  array();
        $this->assertEquals($expected, $result);
    }

    public function testArrayMixedToObject()
    {
        $arr = array('Bloggs', 'firstName' => 'Fred', 9);
        $result = $this->formatter->copyData($arr);

        $expected = '{
            "0": "Bloggs",
            "firstName": "Fred",
            "1": 9
        }';

        $expected = $this->fromJson($expected);
        $this->assertEquals($expected, $result);
    }

    public function testObjectWithEmptyElements()
    {
        $data = new \stdClass();
        $data->prop1 = new \stdClass();
        $data->prop2 = 'none';
        $data->prop3 = null;
        $data->prop4 = array();
        $data->prop5 = array(7);
        $data->prop6 = array('Bloggs', 'firstName' => 'Fred', 9);
        $result = $this->formatter->copyData($data);

        $expected = '{
            "prop1": {},
            "prop2": "none",
            "prop3": null,
            "prop4": [],
            "prop5": [7],
            "prop6": {
                "0": "Bloggs",
                "firstName": "Fred",
                "1": 9
            }
        }';

        $expected = $this->fromJson($expected);
        $this->assertEquals($expected, $result);
    }
}
