<?php

namespace JsonWorks\Tests\Utils;

use JohnStevenson\JsonWorks\Helpers\Data;

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

        $result = Data::toJson(json_decode($data), false);
        $this->assertEquals($expected, $result);
    }

    public function testEscapeSlash()
    {
        $data = '{
            "prop1": "path/to/somewhere"
        }';

        $expected = '{"prop1":"path/to/somewhere"}';

        $data = $this->fromJson($data);
        $result = Data::toJson($data, false);
        $this->assertEquals($expected, $result);

    }

    public function testEscapeUnicode()
    {
        $data = '{
            "prop1": "\\u018c"
        }';

        $expected = '{"prop1":"ƌ"}';

        $data = $this->fromJson($data);
        $result = Data::toJson($data, false);
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
        $result = Data::toJson($data, true);
        $this->assertEquals($expected, $result);
    }
}
