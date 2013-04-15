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

    public function testPushKeyZerosTrue()
    {
        $value = '00000';
        $result = $this->call('pushKey', array($value));
        $this->assertTrue($result);
    }

    public function testPushKeyFalse1()
    {
        $value = '-000';
        $result = $this->call('pushKey', array($value));
        $this->assertFalse($result);
    }

    public function testPushKeyFalse2()
    {
        $value = '9';
        $result = $this->call('pushKey', array($value));
        $this->assertFalse($result);
    }

    public function testArrayKeyTrue()
    {
        $value = '0008';
        $index = null;
        $result = $this->call('arrayKey', array($value, &$index, true));
        $this->assertTrue($result);
    }

    public function testArrayKeyTrue1()
    {
        $value = '0';
        $index = null;
        $result = $this->call('arrayKey', array($value, &$index));
        $this->assertTrue($result);
    }

    public function testArrayKeyTrue2()
    {
        $value = '0007';
        $index = null;
        $result = $this->call('arrayKey', array($value, &$index));
        $this->assertTrue($result);
    }

    public function testArrayKeyFalse1()
    {
        $value = '-';
        $index = null;
        $result = $this->call('arrayKey', array($value, &$index));
        $this->assertFalse($result);
    }

    public function testArrayKeyFalse2()
    {
        $value = 'prop';
        $index = null;
        $result = $this->call('arrayKey', array($value, &$index));
        $this->assertFalse($result);
    }

    public function testArrayKeyAllTrue1()
    {
        $value = '-';
        $index = null;
        $result = $this->call('arrayKey', array($value, &$index, true));
        $this->assertTrue($result);
    }

    public function testArrayKeyAllTrue2()
    {
        $value = '67';
        $index = null;
        $result = $this->call('arrayKey', array($value, &$index, true));
        $this->assertTrue($result);
    }

    public function testArrayKeyAllFalse1()
    {
        $value = '-7';
        $index = null;
        $result = $this->call('arrayKey', array($value, &$index, true));
        $this->assertFalse($result);
    }

    public function testArrayKeyAllFalse2()
    {
        $value = 'prop';
        $index = null;
        $result = $this->call('arrayKey', array($value, &$index, true));
        $this->assertFalse($result);
    }
}

