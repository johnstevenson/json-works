<?php
namespace JsonWorks\Tests\Helpers;

use JohnStevenson\JsonWorks\Helpers\Patch\Builder;

class MethodsTest extends \JsonWorks\Tests\Base
{
    protected function call($name, $args)
    {
        $builder = new Builder();
        return $this->callMethod($builder, $name, $args);
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
        $array = [];
        $index = null;
        $result = $this->call('checkArrayKey', [$array, $value, &$index]);
        $this->assertTrue($result);
    }

    public function testArrayKeyTrue2()
    {
        $value = '67';
        $array = array_fill(0, 68, 1);
        $index = null;
        $result = $this->call('checkArrayKey', [$array, $value, &$index]);
        $this->assertTrue($result);
    }

    public function testArrayKeyFalse1()
    {
        $value = '-7';
        $array = [];
        $index = null;
        $result = $this->call('checkArrayKey', [$array, $value, &$index]);
        $this->assertFalse($result);
    }

    public function testArrayKeyFalse2()
    {
        $value = 'prop';
        $array = [];
        $index = null;
        $result = $this->call('checkArrayKey', [$array, $value, &$index]);
        $this->assertFalse($result);
    }
}
