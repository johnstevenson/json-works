<?php

namespace JsonWorks\Tests\Document;

class LoadSchemaTest extends \PHPUnit_Framework_TestCase
{
    private function getFilename($name)
    {
        return  __DIR__.'/Fixtures/'.$name.'.json';
    }

    /**
    * @expectedException        RuntimeException
    * @expectedExceptionMessage Unable to open file
    *
    */
    public function testWrongFileFail()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
        $filename = 'nofile.json';

        $document->loadSchema($filename);
    }

    public function testWrongFileNoExceptionFail()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
        $filename = 'nofile.json';

        $result = $document->loadSchema($filename, true);
        $expected = 'Unable to open file';

        $this->assertFalse($result);
        $this->assertContains($expected, $document->lastError);
    }

    /**
    * @expectedException        RuntimeException
    * @expectedExceptionMessage Invalid input
    *
    */
    public function testFileEmptyFail()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
        $filename = $this->getFilename('testLoadEmpty');

        $document->loadSchema($filename);
    }

    public function testFileEmptyNoExceptionFail()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
        $filename = $this->getFilename('testLoadEmpty');

        $result = $document->loadSchema($filename, true);
        $expected = 'Invalid input';

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
        $document = new \JohnStevenson\JsonWorks\Document();
        $filename = $this->getFilename('testLoadInvalid');

        $document->loadSchema($filename);
    }

    public function testFileInvalidNoExceptionFail()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
        $filename = $this->getFilename('testLoadInvalid');

        $result = $document->loadSchema($filename, true);
        $expected = 'Invalid input';

        $this->assertFalse($result);
        $this->assertContains($expected, $document->lastError);
    }

    public function testObjectOkay()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
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
        $document = new \JohnStevenson\JsonWorks\Document();
        $data = array();

        $document->loadSchema($data);
    }

    public function testArrayNoExceptionFail()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
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
        $document = new \JohnStevenson\JsonWorks\Document();
        $data = null;

        $document->loadSchema($data);
    }

    public function testNullNoExceptionFail()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
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
        $document = new \JohnStevenson\JsonWorks\Document();
        $data = false;

        $document->loadSchema($data);
    }

    public function testBooleanNoExceptionFail()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
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
        $document = new \JohnStevenson\JsonWorks\Document();
        $data = 0;

        $document->loadSchema($data);
    }

    public function testNumberNoExceptionFail()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
        $data = 0;

        $result = $document->loadSchema($data, true);
        $expected = 'Invalid input';

        $this->assertFalse($result);
        $this->assertContains($expected, $document->lastError);
    }

}
