<?php declare(strict_types=1);

namespace JsonWorks\Tests\BaseDocument;

use JohnStevenson\JsonWorks\BaseDocument;

class LoadingTest extends \JsonWorks\Tests\Base
{
    public function testLoadData(): void
    {
        $document = new BaseDocument();
        $filename = $this->getFixturePath('pretty.json');

        try {
            $document->loadData($filename);
            $result = true;
        } catch (\RuntimeException $e) {
            $result = false;
        }

        self::assertTrue($result);
    }

    public function testLoadScema(): void
    {
        $document = new BaseDocument();
        $filename = $this->getFixturePath('schema.json');

        try {
            $document->loadSchema($filename);
            $result = true;
        } catch (\RuntimeException $e) {
            $result = false;
        }

        self::assertTrue($result);
    }
}
