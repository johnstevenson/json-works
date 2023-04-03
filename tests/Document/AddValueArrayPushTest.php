<?php declare(strict_types=1);

namespace JsonWorks\Tests\Document;

class AddValueArrayPushTest extends \JsonWorks\Tests\Base
{
    public function testRootSingleDash(): void
    {
        $schema = null;
        $data = null;

        $document = $this->getDocument($schema, $data);
        $path = '/-';
        $value = 1;
        $expected = [$value];
        self::assertTrue($document->addValue($path, $value));
        self::assertEquals($expected, $document->data);
    }

    public function testRootSingleZero(): void
    {
        $schema = null;
        $data = null;

        $document = $this->getDocument($schema, $data);
        $path = '/0';
        $value = 1;
        $expected = [$value];
        self::assertTrue($document->addValue($path, $value));
        self::assertEquals($expected, $document->data);
    }

    public function testRootMultiDash(): void
    {
        $document = $this->getDocument(null, null);
        $path = '/-/-/-';
        $value = 1;
        $expected = [[[$value]]];
        self::assertTrue($document->addValue($path, $value));
        self::assertEquals($expected, $document->data);
    }

    public function testRootMultiZero(): void
    {
        $document = $this->getDocument(null, null);
        $path = '/0/0/0';
        $value = 1;
        $expected = [[[$value]]];
        self::assertTrue($document->addValue($path, $value));
        self::assertEquals($expected, $document->data);
    }

    public function testRootMultiMixed(): void
    {
        $document = $this->getDocument(null, null);
        $path = '/-/0/-';
        $value = 1;
        $expected = [[[$value]]];
        self::assertTrue($document->addValue($path, $value));
        self::assertEquals($expected, $document->data);
    }
}
