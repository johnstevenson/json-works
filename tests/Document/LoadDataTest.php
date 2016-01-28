<?php

namespace JsonWorks\Tests\Document;

use JohnStevenson\JsonWorks\Document;

class LoadDataTest extends \JsonWorks\Tests\Base
{
    /**
    * @expectedException        RuntimeException
    * @expectedExceptionMessage Unable to open file
    *
    */
    public function testWrongFileFail()
    {
        $document = new Document();
        $filename = 'nofile.json';

        $document->loadData($filename);
    }

    public function testWrongFileNoExceptionFail()
    {
        $document = new Document();
        $filename = 'nofile.json';

        $result = $document->loadData($filename, true);
        $expected = 'Unable to open file';

        $this->assertFalse($result);
        $this->assertContains($expected, $document->lastError);
    }

    /**
    * @expectedException        RuntimeException
    * @expectedExceptionMessage File is empty
    *
    */
    public function testFileEmptyFail()
    {
        $document = new Document();
        $filename = $this->getFixturePath('empty.json');

        $document->loadData($filename);
    }

    /**
    * @expectedException        RuntimeException
    * @expectedExceptionMessage Invalid input
    *
    */
    public function testFileInvalidFail()
    {
        $document = new Document();
        $filename = $this->getFixturePath('invalid.json');

        $document->loadData($filename);
    }

    public function testFileInvalidNoExceptionFail()
    {
        $document = new Document();
        $filename = $this->getFixturePath('invalid.json');

        $result = $document->loadData($filename, true);
        $expected = 'Invalid input';

        $this->assertFalse($result);
        $this->assertContains($expected, $document->lastError);
    }

    public function testFileOkay()
    {
        $document = new Document();
        $filename = $this->getFixturePath('pretty.json');

        $result = $document->loadData($filename);
        $this->assertTrue($result);
        $this->assertEmpty($document->lastError);
    }

    public function testFileEmptyArrayOkay()
    {
        $document = new Document();
        $filename = $this->getFixturePath('emptyArray.json');

        $result = $document->loadData($filename);
        $this->assertTrue($result);
        $this->assertEmpty($document->lastError);
    }

    public function testObjectOkay()
    {
        $document = new Document();
        $data = new \stdClass();

        $result = $document->loadData($data);
        $this->assertTrue($result);
        $this->assertEmpty($document->lastError);
    }

    public function testArrayOkay()
    {
        $document = new Document();
        $data = array();

        $result = $document->loadData($data);
        $this->assertTrue($result);
        $this->assertEmpty($document->lastError);
    }

    public function testNullOkay()
    {
        $document = new Document();
        $data = null;

        $result = $document->loadData($data);
        $this->assertTrue($result);
        $this->assertEmpty($document->lastError);
    }

    /**
    * @expectedException        RuntimeException
    * @expectedExceptionMessage Invalid input
    *
    */
    public function testBooleanFail()
    {
        $document = new Document();
        $data = false;

        $document->loadData($data);
    }

    public function testBooleanNoExceptionFail()
    {
        $document = new Document();
        $data = false;

        $result = $document->loadData($data, true);
        $expected = 'Invalid input';

        $this->assertFalse($result);
        $this->assertContains($expected, $document->lastError);
    }

    /**
    * @expectedException        RuntimeException
    * @expectedExceptionMessage Invalid input
    *
    */
    public function testNumberFail()
    {
        $document = new Document();
        $data = 0;

        $document->loadData($data);
    }

    public function testNumberNoExceptionFail()
    {
        $document = new Document();
        $data = 0;

        $result = $document->loadData($data, true);
        $expected = 'Invalid input';

        $this->assertFalse($result);
        $this->assertContains($expected, $document->lastError);
    }
}
