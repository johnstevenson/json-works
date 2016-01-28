<?php

namespace JsonWorks\Tests\Document;

use JohnStevenson\JsonWorks\Document;

class LoadSchemaTest extends \JsonWorks\Tests\Base
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

        $document->loadSchema($filename);
    }

    public function testWrongFileNoExceptionFail()
    {
        $document = new Document();
        $filename = 'nofile.json';

        $result = $document->loadSchema($filename, true);
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

        $document->loadSchema($filename);
    }

    public function testFileEmptyNoExceptionFail()
    {
        $document = new Document();
        $filename = $this->getFixturePath('empty.json');

        $result = $document->loadSchema($filename, true);
        $expected = 'File is empty';

        $this->assertFalse($result);
        $this->assertContains($expected, $document->lastError);
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

        $document->loadSchema($filename);
    }

    public function testFileInvalidNoExceptionFail()
    {
        $document = new Document();
        $filename = $this->getFixturePath('invalid.json');

        $result = $document->loadSchema($filename, true);
        $expected = 'Invalid input';

        $this->assertFalse($result);
        $this->assertContains($expected, $document->lastError);
    }

    public function testObjectOkay()
    {
        $document = new Document();
        $data = new \stdClass();

        $result = $document->loadSchema($data);
        $this->assertTrue($result);
        $this->assertEmpty($document->lastError);
    }

    /**
    * @expectedException        RuntimeException
    * @expectedExceptionMessage Invalid input
    *
    */
    public function testArrayFail()
    {
        $document = new Document();
        $data = array();

        $document->loadSchema($data);
    }

    public function testArrayNoExceptionFail()
    {
        $document = new Document();
        $data = array();

        $result = $document->loadSchema($data, true);
        $expected = 'Invalid input';

        $this->assertFalse($result);
        $this->assertContains($expected, $document->lastError);
    }

    /**
    * @expectedException        RuntimeException
    * @expectedExceptionMessage Invalid input
    *
    */
    public function testNullFail()
    {
        $document = new Document();
        $data = null;

        $document->loadSchema($data);
    }

    public function testNullNoExceptionFail()
    {
        $document = new Document();
        $data = null;

        $result = $document->loadSchema($data, true);
        $expected = 'Invalid input';

        $this->assertFalse($result);
        $this->assertContains($expected, $document->lastError);
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

        $document->loadSchema($data);
    }

    public function testBooleanNoExceptionFail()
    {
        $document = new Document();
        $data = false;

        $result = $document->loadSchema($data, true);
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

        $document->loadSchema($data);
    }

    public function testNumberNoExceptionFail()
    {
        $document = new Document();
        $data = 0;

        $result = $document->loadSchema($data, true);
        $expected = 'Invalid input';

        $this->assertFalse($result);
        $this->assertContains($expected, $document->lastError);
    }
}
