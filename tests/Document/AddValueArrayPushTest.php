<?php

namespace JsonWorks\Tests\Document;

class AddValueArrayPushTest extends \JsonWorks\Tests\Base
{
    public function testRootSingleDash()
    {
        $schema = null;
        $data = null;

        $document = $this->getDocument($schema, $data);
        $path = '/-';
        $value = 1;
        $expected = [$value];
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($expected, $document->data);
    }

    public function testRootSingleZero()
    {
        $schema = null;
        $data = null;

        $document = $this->getDocument($schema, $data);
        $path = '/0';
        $value = 1;
        $expected = [$value];
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($expected, $document->data);
    }

    public function testRootMultiDash()
    {
        $document = $this->getDocument(null, null);
        $path = '/-/-/-';
        $value = 1;
        $expected = [[[$value]]];
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($expected, $document->data);
    }

    public function testRootMultiZero()
    {
        $document = $this->getDocument(null, null);
        $path = '/0/0/0';
        $value = 1;
        $expected = [[[$value]]];
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($expected, $document->data);
    }

    public function testRootMultiMixed()
    {
        $document = $this->getDocument(null, null);
        $path = '/-/0/-';
        $value = 1;
        $expected = [[[$value]]];
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($expected, $document->data);
    }
}
