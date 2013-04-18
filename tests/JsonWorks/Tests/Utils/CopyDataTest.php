<?php

namespace JsonWorks\Tests\Utils;

use \JohnStevenson\JsonWorks\Utils as Utils;

class CopyDataTest extends \PHPUnit_Framework_TestCase
{
    public function testFromObject()
    {
        $obj1 = (object) array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj2 = Utils::copyData($obj1);

        $obj1->lastName = 'Smith';
        $expected = 'Bloggs';
        $this->assertEquals($expected, $obj2->lastName);
    }

    public function testFromAssoc()
    {
        $arr = array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj = Utils::copyData($arr);

        $expected = (object) $arr;
        $this->assertEquals($expected, $obj);
    }

    public function testDeepFromObject()
    {
        $obj = (object) array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj1 = (object) array('users' => array($obj));
        $obj2 = Utils::copyData($obj1);

        $obj1->users[0]->lastName = 'Smith';
        $expected = 'Bloggs';
        $this->assertEquals($expected, $obj2->users[0]->lastName);
    }

    public function testDeepFromAssoc()
    {
        $arr = array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj1 = (object) array('users' => array($arr));

        $obj2 = Utils::copyData($obj1);

        $expected = (object) $arr;
        $this->assertEquals($expected, $obj2->users[0]);
    }

    public function testArrayDeepFromObject()
    {
        $obj = (object) array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj1 = (object) array('users' => array($obj));
        $arr1 = array(9, $obj1);

        $arr2 = Utils::copyData($arr1);

        $arr1[1]->users[0]->lastName = 'Smith';
        $expected = 'Bloggs';
        $this->assertEquals($expected, $arr2[1]->users[0]->lastName);
    }

    public function testArrayDeepFromAssoc()
    {
        $arr = array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $obj1 = (object) array('users' => array($arr));
        $arr1 = array(9, $obj1);

        $arr2 = Utils::copyData($arr1);

        $expected = (object) $arr;
        $this->assertEquals($expected, $arr2[1]->users[0]);
    }
}
