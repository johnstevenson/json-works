<?php

namespace JsonWorks\Tests\Utils;

use \JohnStevenson\JsonWorks\Utils;

class DataToJsonTest extends \JsonWorks\Tests\Base
{
    protected function getFileExpected($test)
    {
        $filename = __DIR__.'/Fixtures/'.$test.'.json';
        return $this->getFileExpectedJson($filename);
    }

    public function testNoData()
    {
        $data = '';
        $expected = 'null';

        $result = Utils::dataToJson(json_decode($data), false);
        $this->assertEquals($expected, $result);
    }

    public function testEscapeSlash()
    {
        $data = '{
            "prop1": "path/to/somewhere"
        }';

        $expected = '{"prop1":"path/to/somewhere"}';

        $data = $this->fromJson($data);
        $result = Utils::dataToJson($data, false);
        $this->assertEquals($expected, $result);

    }

    public function testEscapeUnicode()
    {
        if (!function_exists('mb_convert_encoding') && version_compare(PHP_VERSION, '5.4', '<')) {
            $this->markTestSkipped('Test requires the mbstring extension');
        }

        $data = '{
            "prop1": "\\u018c"
        }';

        $expected = '{"prop1":"ƌ"}';

        $data = $this->fromJson($data);
        $result = Utils::dataToJson($data, false);
        $this->assertEquals($expected, $result);
    }

    public function testPretty()
    {
        $data = '{
            "prop1": "",
            "prop2": {
                "inner1": [
                    {"lat": 50, "lng": 120},
                    {"lat": 27, "lng": 3}
                ],
                "inner2": 2
            },
            "prop3": [],
            "prop4": null,
            "prop5": 5,
            "prop6": {}
        }';

        $expected = $this->getFileExpected(__FUNCTION__);

        $data = $this->fromJson($data);
        $result = Utils::dataToJson($data, true);
        $this->assertEquals($expected, $result);
    }

}
