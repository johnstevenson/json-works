<?php

namespace JsonWorks\Tests\Utils;

use \JohnStevenson\JsonWorks\Utils;

class DataCopyTest extends \JsonWorks\Tests\Base
{
    public function testFromObject()
    {
        $obj1 = (object) array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj2 = Utils::dataCopy($obj1);

        $obj1->lastName = 'Smith';
        $expected = 'Bloggs';
        $this->assertEquals($expected, $obj2->lastName);
    }

    public function testFromAssoc()
    {
        $arr = array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj = Utils::dataCopy($arr);

        $expected = (object) $arr;
        $this->assertEquals($expected, $obj);
    }

    public function testDeepFromObject()
    {
        $obj = (object) array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj1 = (object) array('users' => array($obj));
        $obj2 = Utils::dataCopy($obj1);

        $obj1->users[0]->lastName = 'Smith';
        $expected = 'Bloggs';
        $this->assertEquals($expected, $obj2->users[0]->lastName);
    }

    public function testDeepFromAssoc()
    {
        $arr = array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj1 = (object) array('users' => array($arr));

        $obj2 = Utils::dataCopy($obj1);

        $expected = (object) $arr;
        $this->assertEquals($expected, $obj2->users[0]);
    }

    public function testArrayDeepFromObject()
    {
        $obj = (object) array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj1 = (object) array('users' => array($obj));
        $arr1 = array(9, $obj1);

        $arr2 = Utils::dataCopy($arr1);

        $arr1[1]->users[0]->lastName = 'Smith';
        $expected = 'Bloggs';
        $this->assertEquals($expected, $arr2[1]->users[0]->lastName);
    }

    public function testArrayDeepFromAssoc()
    {
        $arr = array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj = (object) array('users' => array($arr));
        $arr1 = array(9, $obj);

        $arr2 = Utils::dataCopy($arr1);

        $expected = (object) $arr;
        $this->assertEquals($expected, $arr2[1]->users[0]);
    }

    public function testObjectFromEmptyObject()
    {
        $obj = new \stdClass();
        $result = Utils::dataCopy($obj);

        $expected =  new \stdClass();
        $this->assertEquals($expected, $result);
    }

    public function testArrayFromEmptyArray()
    {
        $arr = array();
        $result = Utils::dataCopy($arr);
        $expected =  array();
        $this->assertEquals($expected, $result);
    }

    public function testArrayMixedToObject()
    {
        $arr = array('Bloggs', 'firstName' => 'Fred', 9);
        $result = Utils::dataCopy($arr);

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
        $result = Utils::dataCopy($data);

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
