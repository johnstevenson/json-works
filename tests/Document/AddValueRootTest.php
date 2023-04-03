<?php declare(strict_types=1);

namespace JsonWorks\Tests\Document;

class AddValueRootTest extends \JsonWorks\Tests\Base
{
    public function testObject(): void
    {
        $document = $this->getDocument(null, null);
        $path = '';
        $value = (object) ['prop1' => 1, 'prop2' => 'value'];
        self::assertTrue($document->addValue($path, $value));
        self::assertEquals($value, $document->data);
    }

    public function testArray(): void
    {
        $document = $this->getDocument(null, null);
        $path = '';
        $value = [1, 'value'];
        self::assertTrue($document->addValue($path, $value));
        self::assertEquals($value, $document->data);
    }

    public function testScalar(): void
    {
        $document = $this->getDocument(null, null);
        $path = '';
        $value = 'value';
        self::assertTrue($document->addValue($path, $value));
        self::assertEquals($value, $document->data);
    }
}
