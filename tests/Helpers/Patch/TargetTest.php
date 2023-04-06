<?php declare(strict_types=1);

namespace JsonWorks\Tests\Helpers\Patch;

use JohnStevenson\JsonWorks\Helpers\Error;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;

class TargetTest extends \JsonWorks\Tests\Base
{
    /**
     * @param string|int|false $value
     */
    protected function getMsg(string $key, $value): string
    {
        return sprintf('Testing "%s" is "%s"', $key, $value);
    }

    public function testConstructor(): void
    {
        $error = '';
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
            // @phpstan-ignore-next-line
            self::assertEquals($target->$key, $value, $msg);
        }

        $msg = 'Test error is a reference';
        $error = 'my error';
        self::assertEquals($error, $target->error, $msg);
    }

    public function testConstructorNoPath(): void
    {
        $error = '';
        $target = new Target('', $error);

        self::assertEmpty($target->tokens);
    }

    public function testConstructorValidPath(): void
    {
        $error = '';
        $target = new Target('/prop1/prop2', $error);

        self::assertCount(2, $target->tokens);
    }

    public function testConstructorInvalidPath(): void
    {
        $error = '';
        $target = new Target('invalid/key', $error);

        self::assertTrue($target->invalid);
        self::assertStringContainsString('ERR_PATH_KEY', $target->error);
    }

    public function testSetArray(): void
    {
        $error = '';
        $target = new Target('/prop1/prop2', $error);

        $value = '3';
        $target->setArray($value);

        $msg = $this->getMsg('type', Target::TYPE_ARRAY);
        self::assertEquals($target->type, Target::TYPE_ARRAY, $msg);

        $msg = sprintf('Testing key is integer "%s"', $value);
        self::assertEquals((int) $value, $target->key, $msg);
    }

    public function testSetObject(): void
    {
        $error = '';
        $target = new Target('/prop1/prop2', $error);

        $value = 'prop1';
        $target->setObject($value);

        $msg = $this->getMsg('type', Target::TYPE_OBJECT);
        self::assertEquals($target->type, Target::TYPE_OBJECT, $msg);

        $msg = $this->getMsg('key', $value);
        self::assertEquals($value, $target->key, $msg);
    }

    public function testSetError(): void
    {
        $error = '';
        $target = new Target('/prop1/prop2', $error);

        $value = Error::ERR_PATH_KEY;
        $target->setError($value);

        $msg = 'Testing error is not empty';
        self::assertNotEmpty($target->error, $msg);

        // check null clears error
        $target->setError(null);

        $msg = 'Testing error is empty';
        self::assertEmpty($target->error, $msg);
    }

    public function testSetResult(): void
    {
        $error = '';
        $target = new Target('/prop1/prop2', $error);

        $element = [];

        $target->setResult(true, $element);

        self::assertTrue($this->sameRef($element, $target->element));
    }

    public function testSetResultFalseSetsError(): void
    {
        $error = '';
        $target = new Target('/prop1/prop2', $error);

        $target->setResult(false, $element);

        self::assertStringContainsString('ERR_NOT_FOUND', $target->error);
    }

    public function testSetResultFalsePreservesExistingError(): void
    {
        $error = '';
        $target = new Target('/prop1/prop2', $error);

        // set an error
        $errorCode = Error::ERR_PATH_KEY;
        $target->setError($errorCode);

        // set result
        $target->setResult(false, $element);

        self::assertStringContainsString('ERR_PATH_KEY', $target->error);
    }
}
