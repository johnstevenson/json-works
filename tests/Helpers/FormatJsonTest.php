<?php declare(strict_types=1);

namespace JsonWorks\Tests\Helpers;

use JohnStevenson\JsonWorks\Helpers\Formatter;

class FormatJsonTest extends \JsonWorks\Tests\Base
{
    protected Formatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new Formatter();
    }

    public function testReplacesEmptyKeys(): void
    {
        $expected = $this->getFixtureFile('nullkeys.json');
        $data = $this->decodeJson($expected);

        $result = $this->formatter->toJson($data, JSON_PRETTY_PRINT);
        self::assertEquals($expected, $result);
    }
}
