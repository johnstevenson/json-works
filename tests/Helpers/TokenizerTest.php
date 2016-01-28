<?php

namespace JsonWorks\Tests\Helpers;

use JohnStevenson\JsonWorks\Helpers\Tokenizer;

class TokenizerTest extends \PHPUnit_Framework_TestCase
{
    protected $tokenizer;

    protected function setUp()
    {
        $this->tokenizer = new Tokenizer();
    }

    public function testAddPlain()
    {
        $path = '/prop1';
        $value = 'prop2';
        $expected = '/prop1/prop2';
        $this->assertEquals($expected, $this->tokenizer->add($path, $value));
    }

    public function testAddWithSlash()
    {
        $path = '/prop1';
        $value = 'name/with/slash';
        $expected = '/prop1/name~1with~1slash';
        $this->assertEquals($expected, $this->tokenizer->add($path, $value));
    }

    public function testAddWithTilde()
    {
        $path = '/prop1';
        $value = 'name~with~tilde';
        $expected = '/prop1/name~0with~0tilde';
        $this->assertEquals($expected, $this->tokenizer->add($path, $value));
    }

    public function testAddWithSlashAndTilde()
    {
        $path = '/prop1';
        $value = 'name/with/slash~and~tilde';
        $expected = '/prop1/name~1with~1slash~0and~0tilde';
        $this->assertEquals($expected, $this->tokenizer->add($path, $value));
    }

    public function testAddPathEmpty()
    {
        $path = '';
        $value = 'prop1';
        $expected = '/prop1';
        $this->assertEquals($expected, $this->tokenizer->add($path, $value));
    }

    public function testAddPathEmptyWithEmpty()
    {
        $path = '';
        $value = '';
        $expected = '';
        $this->assertEquals($expected, $this->tokenizer->add($path, $value));
    }

    public function testAddPathEmptyWithZero()
    {
        $path = '';
        $value = '0';
        $expected = '/0';
        $this->assertEquals($expected, $this->tokenizer->add($path, $value));
    }

    public function testDecodePathPlain()
    {
        $value = '/prop1/prop2/prop3';
        $expected = array('prop1', 'prop2', 'prop3');
        $this->assertTrue($this->tokenizer->decode($value, $tokens));
        $this->assertEquals($expected, $tokens);
    }

    public function testDecodePathEncoded()
    {
        $value = '/key~01/key~02~1sub';
        $expected = array('key~1', 'key~2/sub');
        $this->assertTrue($this->tokenizer->decode($value, $tokens));
        $this->assertEquals($expected, $tokens);
    }

    public function testEncodePathPlain()
    {
        $value = 'key1';
        $expected = '/key1';
        $this->assertEquals($expected, $this->tokenizer->encode($value));
    }

    public function testEncodePathWithEncodeChars()
    {
        $value = array('key~1', 'key~2//sub', 'key3');
        $expected = '/key~01/key~02~1~1sub/key3';
        $this->assertEquals($expected, $this->tokenizer->encode($value));
    }

    public function testEncodePathEmpty()
    {
        $value = '';
        $expected = '';
        $this->assertEquals($expected, $this->tokenizer->encode($value));
    }

    public function testEncodePathWithZero()
    {
        $value = '0';
        $expected = '/0';
        $this->assertEquals($expected, $this->tokenizer->encode($value));
    }
}
