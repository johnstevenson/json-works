<?php

namespace JsonWorks\Tests\Document;

use JohnStevenson\JsonWorks\Document;

class LoadSchemaTest extends \JsonWorks\Tests\Base
{
    public function testObjectOkay()
    {
        $document = new Document();
        $data = new \stdClass();

        try {
            $document->loadSchema($data);
            $result = true;
        } catch (\RuntimeException $e) {
            $result = false;
        }

        $this->assertTrue($result);
    }

    /**
    * @expectedException        RuntimeException
    * @expectedExceptionMessage Invalid input
    *
    */
    public function testArrayFail()
    {
        $document = new Document();
        $data = [];

        $document->loadSchema($data);
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
}
