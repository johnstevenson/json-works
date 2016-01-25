<?php

namespace JsonWorks\Tests\Helpers\Patch;

use JohnStevenson\JsonWorks\Helpers\Error;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;

class TargetTest extends \PHPUnit_Framework_TestCase
{
    protected function getMsg($key, $value)
    {
        return sprintf('Testing "%s" is "%s"', $key, $value);
    }

    public function testConstructor()
    {
        $target = new Target('', $error);

        $tests = [
            'type' => Target::TYPE_VALUE,
            'key' => '',
            'childKey' => '',
            'error' => '',
            'errorCode' => 0,
        ];

        foreach ($tests as $key => $value) {
            $msg = $this->getMsg($key, $value);
            $this->assertEquals($target->$key, $value, $msg);
        }

        $msg = 'Test error is a reference';
        $error = 'my error';
        $this->assertEquals($error, $target->error, $msg);
    }

    public function testConstructorNoPath()
    {
        $target = new Target('', $error);

        $msg = 'Test tokens is empty';
        $this->assertEmpty($target->tokens, $msg);

        $msg = 'Test found is true';
        $this->assertTrue($target->found, $msg);
    }

    public function testConstructorValidPath()
    {
        $target = new Target('/prop1/prop2', $error);

        $msg = 'Test tokens contains 2 entries';
        $this->assertCount(2, $target->tokens, $msg);

        $msg = 'Test found is false';
        $this->assertFalse($target->found, $msg);
    }

    public function testConstructorInvalidPath()
    {
        $target = new Target('/invalid//key', $error);

        $msg = 'Test found is false with invalid path';
        $this->assertFalse($target->found, $msg);

        $msg = 'Test tokens have been emtpied with invalid path';
        $this->assertEmpty($target->tokens, $msg);

        $msg = 'Testing error contains ERR_KEY_EMPTY';
        $this->assertContains('ERR_KEY_EMPTY', $target->error);

        $errorCode = Error::ERR_KEY_EMPTY;
        $msg = $this->getMsg('errorCode', $errorCode);
        $this->assertEquals($errorCode, $target->errorCode, $msg);
    }

    public function testSetArray()
    {
        $target = new Target('/prop1/prop2', $error);

        $value = '3';
        $target->setArray($value);

        $msg = $this->getMsg('type', Target::TYPE_ARRAY);
        $this->assertEquals($target->type, Target::TYPE_ARRAY, $msg);

        $msg = sprintf('Testing key is integer "%s"', $value);
        $this->assertEquals((int) $value, $target->key, $msg);
    }

    public function testSetObject()
    {
        $target = new Target('/prop1/prop2', $error);

        $value = 'prop1';
        $target->setObject($value);

        $msg = $this->getMsg('type', Target::TYPE_OBJECT);
        $this->assertEquals($target->type, Target::TYPE_OBJECT, $msg);

        $msg = $this->getMsg('key', $value);
        $this->assertEquals($value, $target->key, $msg);
    }

    public function testSetError()
    {
        $target = new Target('/prop1/prop2', $error);

        $value = Error::ERR_KEY_INVALID;
        $target->setError($value);

        $msg = 'Testing error is not empty';
        $this->assertNotEmpty($target->error, $msg);

        $msg = $this->getMsg('errorCode', $value);
        $this->assertEquals($value, $target->errorCode, $msg);

        // check null clears error
        $target->setError(null);

        $tests = [
            'error' => '',
            'errorCode' => 0,
        ];

        foreach ($tests as $key => $value) {
            $msg = $this->getMsg($key, $value);
            $this->assertEquals($target->$key, $value, $msg);
        }
    }

    public function testSetFound()
    {
        $target = new Target('/prop1/prop2', $error);

        $value = !$target->found;
        $target->setFound($value);

        $msg = $this->getMsg('key', $value);
        $this->assertEquals($value, $target->found, $msg);
    }

    public function testSetFoundFalseSetsError()
    {
        $target = new Target('/prop1/prop2', $error);
        $target->setFound(false);

        $msg = 'Testing error contains ERR_NOT_FOUND';
        $this->assertContains('ERR_NOT_FOUND', $target->error);

        $errorCode = Error::ERR_NOT_FOUND;
        $msg = $this->getMsg('errorCode', $errorCode);
        $this->assertEquals($errorCode, $target->errorCode, $msg);
    }

    public function testSetFoundFalsePreservesExistingError()
    {
        $target = new Target('/prop1/prop2', $error);

        // set an error
        $errorCode = Error::ERR_KEY_INVALID;
        $target->setError($errorCode);

        // set found
        $target->setFound(false);

        $msg = 'Testing error contains original ERR_KEY_INVALID';
        $this->assertContains('ERR_KEY_INVALID', $target->error);

        $msg = $this->getMsg('errorCode', $errorCode);
        $this->assertEquals($errorCode, $target->errorCode, $msg);
    }
}
