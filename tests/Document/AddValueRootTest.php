<?php

namespace JsonWorks\Tests\Document;

class AddValueRootTest extends \JsonWorks\Tests\Base
{
    public function testObject()
    {
        $document = $this->getDocument(null, null);
        $path = '';
        $value = (object) ['prop1' => 1, 'prop2' => 'value'];
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($value, $document->data);
    }

    public function testArray()
    {
        $document = $this->getDocument(null, null);
        $path = '';
        $value = [1, 'value'];
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($value, $document->data);
    }

    public function testInvalid()
    {
        $document = $this->getDocument(null, null);
        $path = '';
        $value = 'value';
        $this->assertFalse($document->addValue($path, $value));
        $this->assertNull($document->data);

    }
}
