<?php

namespace JsonWorks\Tests\Document;

use JohnStevenson\JsonWorks\Document;

class LoadDataTest extends \JsonWorks\Tests\Base
{
    public function testWrongFileFail()
    {
        $document = new Document();
        $filename = 'nofile.json';

        $this->setExpectedException('RuntimeException', 'ERR_NOT_FOUND');
        $document->loadData($filename, false);
    }

    public function testFileInvalidFail()
    {
        $document = new Document();
        $filename = $this->getFixturePath('invalid.json');

        $this->setExpectedException('RuntimeException', 'ERR_BAD_INPUT');
        $document->loadData($filename, false);
    }

    public function testFileOkay()
    {
        $document = new Document();
        $filename = $this->getFixturePath('pretty.json');

        try {
            $document->loadData($filename, false);
            $result = true;
        } catch (\RuntimeException $e) {
            $result = false;
        }

        $this->assertTrue($result);
    }

    public function testResourceFail()
    {
        $document = new Document();
        $filename = $this->getFixturePath('pretty.json');
        $data = fopen($filename, 'r');

        $this->setExpectedException('RuntimeException', 'ERR_BAD_INPUT');
        $document->loadData($data, false);
    }
}
