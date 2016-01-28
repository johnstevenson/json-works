<?php

namespace JsonWorks\Tests\Helpers;

use JohnStevenson\JsonWorks\Helpers\Formatter;

class FormatJsonTest extends \JsonWorks\Tests\Base
{
    protected $formatter;

    protected function setUp()
    {
        $this->formatter = new Formatter();
    }

    public function testReplacesEmptyKeys()
    {
        $expected = $this->getFixtureFile('emptyKeys.json');
        $data = $this->fromJson($expected);

        $result = $this->formatter->toJson($data, JSON_PRETTY_PRINT);
        $this->assertEquals($expected, $result);
    }
}
