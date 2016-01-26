<?php
namespace JsonWorks\Tests\Helpers\Builder;

use JohnStevenson\JsonWorks\Helpers\Patch\Builder;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;

class MethodsTest extends \JsonWorks\Tests\Base
{
    protected $builder;

    protected function setUp()
    {
        $this->builder = new Builder();
        $target = new Target('', $dummy);
        $data = null;
        $value = null;
        $this->builder->make($target, $value);
    }

    protected function call($name, $args)
    {
        return $this->callMethod($this->builder, $name, $args);
    }

    public function testPushKeyDashTrue()
    {
        $value = '-';
        $result = $this->call('isPushKey', [$value]);
        $this->assertTrue($result);
    }

    public function testPushKeyZeroTrue()
    {
        $value = '0';
        $result = $this->call('isPushKey', [$value]);
        $this->assertTrue($result);
    }

    public function testPushKeyFalse1()
    {
        $value = '00000';
        $result = $this->call('isPushKey', [$value]);
        $this->assertFalse($result);
    }

    public function testPushKeyFalse2()
    {
        $value = '-000';
        $result = $this->call('isPushKey', [$value]);
        $this->assertFalse($result);
    }

    public function testPushKeyFalse3()
    {
        $value = '9';
        $result = $this->call('isPushKey', [$value]);
        $this->assertFalse($result);
    }

    public function testArrayKeyTrue1()
    {
        $value = '-';
        $array = [1, 2, 3];
        $index = null;
        $expected = count($array);

        try {
            $this->call('checkArrayKey', [$array, $value, &$index]);
            $result = true;
        } catch (\InvalidArgumentException $e) {
            $result = false;
        }

        $this->assertTrue($result);
        $this->assertEquals($expected, $index);
    }

    public function testArrayKeyTrue2()
    {
        $value = '67';
        $array = array_fill(0, 68, 1);
        $index = null;
        $expected = (int) $value;

        try {
            $this->call('checkArrayKey', [$array, $value, &$index]);
            $result = true;
        } catch (\InvalidArgumentException $e) {
            $result = false;
        }

        $this->assertTrue($result);
        $this->assertEquals($expected, $index);
    }

    public function testArrayKeyFalse1()
    {
        $value = '-7';
        $array = [];
        $index = null;

        $this->setExpectedException('InvalidArgumentException');
        $this->call('checkArrayKey', [$array, $value, &$index]);
    }

    public function testArrayKeyFalse2()
    {
        $value = 'prop';
        $array = [];
        $index = null;

        $this->setExpectedException('InvalidArgumentException');
        $this->call('checkArrayKey', [$array, $value, &$index]);
    }
}
