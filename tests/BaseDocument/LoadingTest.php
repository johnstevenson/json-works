<?php

namespace JsonWorks\Tests\BaseDocument;

use JohnStevenson\JsonWorks\BaseDocument;

class LoadingTest extends \JsonWorks\Tests\Base
{
    public function testLoadData()
    {
        $document = new BaseDocument();
        $filename = $this->getFixturePath('pretty.json');

        try {
            $document->loadData($filename);
            $result = true;
        } catch (\RuntimeException $e) {
            $result = false;
        }

        $this->assertTrue($result);
    }

    public function testLoadScema()
    {
        $document = new BaseDocument();
        $filename = $this->getFixturePath('schema.json');

        try {
            $document->loadSchema($filename, $filename);
            $result = true;
        } catch (\RuntimeException $e) {
            $result = false;
        }

        $this->assertTrue($result);
    }
}
