<?php
namespace JsonWorks\Tests\Document;

class MethodsTest extends \JsonWorks\Tests\Base
{
    protected function call($name, $args)
    {
        $document = $this->getDocument(null, null);
        return $this->callMethod($document, $name, $args);
    }

    public function testPushKeyDashTrue()
    {
        $value = '-';
        $result = $this->call('pushKey', array($value));
        $this->assertTrue($result);
    }

    public function testPushKeyZeroTrue()
    {
        $value = '0';
        $result = $this->call('pushKey', array($value));
        $this->assertTrue($result);
    }

    public function testPushKeyFalse1()
    {
        $value = '00000';
        $result = $this->call('pushKey', array($value));
        $this->assertFalse($result);
    }

    public function testPushKeyFalse2()
    {
        $value = '-000';
        $result = $this->call('pushKey', array($value));
        $this->assertFalse($result);
    }

    public function testPushKeyFalse3()
    {
        $value = '9';
        $result = $this->call('pushKey', array($value));
        $this->assertFalse($result);
    }

    public function testArrayKeyAllTrue1()
    {
        $value = '-';
        $index = null;
        $result = $this->call('arrayKey', array($value, &$index));
        $this->assertTrue($result);
    }

    public function testArrayKeyAllTrue2()
    {
        $value = '67';
        $index = null;
        $result = $this->call('arrayKey', array($value, &$index));
        $this->assertTrue($result);
    }

    public function testArrayKeyAllFalse1()
    {
        $value = '-7';
        $index = null;
        $result = $this->call('arrayKey', array($value, &$index));
        $this->assertFalse($result);
    }

    public function testArrayKeyAllFalse2()
    {
        $value = 'prop';
        $index = null;
        $result = $this->call('arrayKey', array($value, &$index));
        $this->assertFalse($result);
    }
}
