<?php

namespace JsonWorks\Tests\Helpers\Patch;

use JohnStevenson\JsonWorks\Helpers\Error;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;

class TargetTest extends \JsonWorks\Tests\Base
{
    protected function getMsg($key, $value)
    {
        return sprintf('Testing "%s" is "%s"', $key, $value);
    }

    public function testConstructor()
    {
        $target = new Target('', $error);

        $tests = [
            'invalid' => false,
            'type' => Target::TYPE_VALUE,
            'key' => '',
            'childKey' => '',
            'error' => '',
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

        $this->assertEmpty($target->tokens);
    }

    public function testConstructorValidPath()
    {
        $target = new Target('/prop1/prop2', $error);

        $this->assertCount(2, $target->tokens);
    }

    public function testConstructorInvalidPath()
    {
        $target = new Target('/invalid//key', $error);

        $this->assertTrue($target->invalid);
        $this->assertContains('ERR_PATH_KEY', $target->error);
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

        $value = Error::ERR_PATH_KEY;
        $target->setError($value);

        $msg = 'Testing error is not empty';
        $this->assertNotEmpty($target->error, $msg);

        // check null clears error
        $target->setError(null);

        $msg = 'Testing error is empty';
        $this->assertEmpty($target->error, $msg);
    }

    public function testSetResult()
    {
        $target = new Target('/prop1/prop2', $error);

        $element = [];

        $target->setResult(true, $element);

        $this->assertTrue($this->sameRef($element, $target->element));
    }

    public function testSetResultFalseSetsError()
    {
        $target = new Target('/prop1/prop2', $error);

        $target->setResult(false, $element);

        $this->assertContains('ERR_NOT_FOUND', $target->error);
    }

    public function testSetResultFalsePreservesExistingError()
    {
        $target = new Target('/prop1/prop2', $error);

        // set an error
        $errorCode = Error::ERR_PATH_KEY;
        $target->setError($errorCode);

        // set result
        $target->setResult(false, $element);

        $this->assertContains('ERR_PATH_KEY', $target->error);
    }
}
