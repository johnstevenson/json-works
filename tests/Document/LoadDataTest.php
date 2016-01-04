<?php

namespace JsonWorks\Tests\Document;

class LoadDataTest extends \PHPUnit_Framework_TestCase
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

        $document->loadData($filename);
    }

    public function testWrongFileNoExceptionFail()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
        $filename = 'nofile.json';

        $result = $document->loadData($filename, true);
        $expected = 'Unable to open file';

        $this->assertFalse($result);
        $this->assertContains($expected, $document->lastError);
    }

    public function testFileEmptyOkay()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
        $filename = $this->getFilename('testLoadEmpty');

        $result = $document->loadData($filename);
        $this->assertTrue($result);
        $this->assertEmpty($document->lastError);
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

        $document->loadData($filename);
    }

    public function testFileInvalidNoExceptionFail()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
        $filename = $this->getFilename('testLoadInvalid');

        $result = $document->loadData($filename, true);
        $expected = 'Invalid input';

        $this->assertFalse($result);
        $this->assertContains($expected, $document->lastError);
    }

    public function testFileOkay()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
        $filename = $this->getFilename('testLoadOkay');

        $result = $document->loadData($filename);
        $this->assertTrue($result);
        $this->assertEmpty($document->lastError);
    }

    public function testFileEmptyArrayOkay()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
        $filename = $this->getFilename('testLoadEmptyArray');

        $result = $document->loadData($filename);
        $this->assertTrue($result);
        $this->assertEmpty($document->lastError);
    }

    public function testObjectOkay()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
        $data = new \stdClass();

        $result = $document->loadData($data);
        $this->assertTrue($result);
        $this->assertEmpty($document->lastError);
    }

    public function testArrayOkay()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
        $data = array();

        $result = $document->loadData($data);
        $this->assertTrue($result);
        $this->assertEmpty($document->lastError);
    }

    public function testNullOkay()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
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
        $document = new \JohnStevenson\JsonWorks\Document();
        $data = false;

        $document->loadData($data);
    }

    public function testBooleanNoExceptionFail()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
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
        $document = new \JohnStevenson\JsonWorks\Document();
        $data = 0;

        $document->loadData($data);
    }

    public function testNumberNoExceptionFail()
    {
        $document = new \JohnStevenson\JsonWorks\Document();
        $data = 0;

        $result = $document->loadData($data, true);
        $expected = 'Invalid input';

        $this->assertFalse($result);
        $this->assertContains($expected, $document->lastError);
    }
}
