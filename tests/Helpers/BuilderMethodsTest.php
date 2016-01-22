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
        $result = $this->call('isPushKey', array($value));
        $this->assertTrue($result);
    }

    public function testPushKeyZeroTrue()
    {
        $value = '0';
        $result = $this->call('isPushKey', array($value));
        $this->assertTrue($result);
    }

    public function testPushKeyFalse1()
    {
        $value = '00000';
        $result = $this->call('isPushKey', array($value));
        $this->assertFalse($result);
    }

    public function testPushKeyFalse2()
    {
        $value = '-000';
        $result = $this->call('isPushKey', array($value));
        $this->assertFalse($result);
    }

    public function testPushKeyFalse3()
    {
        $value = '9';
        $result = $this->call('isPushKey', array($value));
        $this->assertFalse($result);
    }

    public function testArrayKeyTrue1()
    {
        $value = '-';
        $index = null;
        $result = $this->call('isArrayKey', array($value, &$index));
        $this->assertTrue($result);
    }

    public function testArrayKeyTrue2()
    {
        $value = '67';
        $index = null;
        $result = $this->call('isArrayKey', array($value, &$index));
        $this->assertTrue($result);
    }

    public function testArrayKeyFalse1()
    {
        $value = '-7';
        $index = null;
        $result = $this->call('isArrayKey', array($value, &$index));
        $this->assertFalse($result);
    }

    public function testArrayKeyFalse2()
    {
        $value = 'prop';
        $index = null;
        $result = $this->call('isArrayKey', array($value, &$index));
        $this->assertFalse($result);
    }
}
