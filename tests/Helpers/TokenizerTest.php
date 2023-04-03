<?php declare(strict_types=1);

namespace JsonWorks\Tests\Helpers;

use JohnStevenson\JsonWorks\Helpers\Tokenizer;

class TokenizerTest extends \PHPUnit\Framework\TestCase
{
    protected Tokenizer $tokenizer;

    protected function setUp(): void
    {
        $this->tokenizer = new Tokenizer();
    }

    public function testAddPlain(): void
    {
        $path = '/prop1';
        $value = 'prop2';
        $expected = '/prop1/prop2';
        self::assertEquals($expected, $this->tokenizer->add($path, $value));
    }

    public function testAddWithSlash(): void
    {
        $path = '/prop1';
        $value = 'name/with/slash';
        $expected = '/prop1/name~1with~1slash';
        self::assertEquals($expected, $this->tokenizer->add($path, $value));
    }

    public function testAddWithTilde(): void
    {
        $path = '/prop1';
        $value = 'name~with~tilde';
        $expected = '/prop1/name~0with~0tilde';
        self::assertEquals($expected, $this->tokenizer->add($path, $value));
    }

    public function testAddWithSlashAndTilde(): void
    {
        $path = '/prop1';
        $value = 'name/with/slash~and~tilde';
        $expected = '/prop1/name~1with~1slash~0and~0tilde';
        self::assertEquals($expected, $this->tokenizer->add($path, $value));
    }

    public function testAddPathEmpty(): void
    {
        $path = '';
        $value = 'prop1';
        $expected = '/prop1';
        self::assertEquals($expected, $this->tokenizer->add($path, $value));
    }

    public function testAddPathEmptyWithEmpty(): void
    {
        $path = '';
        $value = '';
        $expected = '';
        self::assertEquals($expected, $this->tokenizer->add($path, $value));
    }

    public function testAddPathEmptyWithZero(): void
    {
        $path = '';
        $value = '0';
        $expected = '/0';
        self::assertEquals($expected, $this->tokenizer->add($path, $value));
    }

    public function testDecodePathPlain(): void
    {
        $value = '/prop1/prop2/prop3';
        $expected = array('prop1', 'prop2', 'prop3');
        self::assertTrue($this->tokenizer->decode($value, $tokens));
        self::assertEquals($expected, $tokens);
    }

    public function testDecodePathEncoded(): void
    {
        $value = '/key~01/key~02~1sub';
        $expected = array('key~1', 'key~2/sub');
        self::assertTrue($this->tokenizer->decode($value, $tokens));
        self::assertEquals($expected, $tokens);
    }

    public function testDecodeFailsWithInvalidPath(): void
    {
        $msg = 'Testing failure with missing root slash';
        $value = 'prop1/prop2/prop3';
        self::assertFalse($this->tokenizer->decode($value, $tokens), $msg);

        $msg = 'Testing failure with # ref at root';
        $value = '#prop1/prop2/prop3';
        self::assertFalse($this->tokenizer->decode($value, $tokens), $msg);
    }

    public function testEncodePathPlain(): void
    {
        $value = 'key1';
        $expected = '/key1';
        self::assertEquals($expected, $this->tokenizer->encode($value));
    }

    public function testEncodePathWithEncodeChars(): void
    {
        $value = array('key~1', 'key~2//sub', 'key3');
        $expected = '/key~01/key~02~1~1sub/key3';
        self::assertEquals($expected, $this->tokenizer->encode($value));
    }

    public function testEncodePathEmpty(): void
    {
        $value = '';
        $expected = '';
        self::assertEquals($expected, $this->tokenizer->encode($value));
    }

    public function testEncodePathWithZero(): void
    {
        $value = '0';
        $expected = '/0';
        self::assertEquals($expected, $this->tokenizer->encode($value));
    }
}
