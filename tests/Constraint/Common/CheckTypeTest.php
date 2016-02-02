<?php

namespace JsonWorks\Tests\Constraint\Common;

use JohnStevenson\JsonWorks\Schema\Constraints\Manager;
use JohnStevenson\JsonWorks\Schema\Constraints\TypeConstraint;

class CheckTypeTest extends \JsonWorks\Tests\Base
{
    protected $type;

    protected function setUp()
    {
        $manager = new Manager(false);
        $this->type = new TypeConstraint($manager);
    }

    public function testArray()
    {
        $value = [1, 2, 3];
        $result = $this->callMethod($this->type, 'checkType', [$value, 'array']);
        $this->assertTrue($result);
    }

    public function testObject()
    {
        $value = (object) ['name' => 'value'];
        $result = $this->callMethod($this->type, 'checkType', [$value, 'object']);
        $this->assertTrue($result);
    }

    public function testNull()
    {
        $value = null;
        $result = $this->callMethod($this->type, 'checkType', [$value, 'null']);
        $this->assertTrue($result);
    }

    public function testBoolean()
    {
        $value = false;
        $result = $this->callMethod($this->type, 'checkType', [$value, 'boolean']);
        $this->assertTrue($result);
    }

    public function testString()
    {
        $value = 'test string';
        $result = $this->callMethod($this->type, 'checkType', [$value, 'string']);
        $this->assertTrue($result);
    }

    public function testIntegerAsInteger()
    {
        $value = 21;
        $result = $this->callMethod($this->type, 'checkType', [$value, 'integer']);
        $this->assertTrue($result);
    }

    public function testIntegerArbitrarilyLarge()
    {
        $value = PHP_INT_MAX + 100;
        $result = $this->callMethod($this->type, 'checkType', [$value, 'integer']);
        $this->assertTrue($result);
    }

    public function testIntegerAsNumber()
    {
        $value = 21;
        $result = $this->callMethod($this->type, 'checkType', [$value, 'number']);
        $this->assertTrue($result);
    }

    public function testFloatAsNumber()
    {
        $value = 21.2;
        $result = $this->callMethod($this->type, 'checkType', [$value, 'number']);
        $this->assertTrue($result);
    }

    public function testFloatNotInteger()
    {
        $value = 21.2;
        $result = $this->callMethod($this->type, 'checkType', [$value, 'integer']);
        $this->assertFalse($result);
    }

    public function testUnknown()
    {
        $value = [1, 2, 3];
        $result = $this->callMethod($this->type, 'checkType', [$value, 'unknown']);
        $this->assertFalse($result);
    }

    public function testFail()
    {
        $value = [1, 2, 3];
        $result = $this->callMethod($this->type, 'checkType', [$value, 'object']);
        $this->assertFalse($result);
    }
}
