<?php

namespace JsonWorks\Tests\Utils;

use \JohnStevenson\JsonWorks\Utils;

class GetTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayPass()
    {
        $container = array('name' => 'value');
        $this->assertEquals('value', Utils::get($container, 'name'));
    }

    public function testArrayFail()
    {
        $container = array('name' => 'value');
        $this->assertEquals(null, Utils::get($container, 'name1'));
    }

    public function testArrayFailWithDefault()
    {
        $container = array('name' => 'value');
        $this->assertEquals(array(), Utils::get($container, 'name1', array()));
    }

    public function testObjectPass()
    {
        $container = (object) array('name' => 'value');
        $this->assertEquals('value', Utils::get($container, 'name'));
    }

    public function testObjectFail()
    {
        $container = (object) array('name' => 'value');
        $this->assertEquals(null, Utils::get($container, 'name1'));
    }

    public function testObjectFailWithDefault()
    {
        $container = (object) array('name' => 'value');
        $this->assertEquals(array(), Utils::get($container, 'name1', array()));
    }

    public function testScalarFail()
    {
        $container = 6;
        $this->assertEquals(null, Utils::get($container, 'name'));
    }
}

