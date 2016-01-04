<?php

namespace JsonWorks\Tests\Utils;

use \JohnStevenson\JsonWorks\Utils;

class DataPruneTest extends \JsonWorks\Tests\Base
{
    public function testNoData()
    {
        $data = '';
        $expected = '';

        $result = Utils::dataPrune(json_decode($data));
        $this->assertEquals(json_decode($expected), $result);
    }

    public function testSimple()
    {
        $data = '{
            "prop1": "",
            "prop2": {},
            "prop3": [],
            "prop4": null
        }';

        $expected = '{
            "prop1": "",
            "prop4": null
        }';

        $data = $this->fromJson($data);
        $expected = $this->fromJson($expected);

        $result = Utils::dataPrune($data);
        $this->assertEquals($expected, $result);
    }

    public function testNested()
    {
        $data = '{
            "prop1": {
                "nested1": {
                    "nested2": {
                        "inner1": ["hidden"],
                        "inner2": {}
                    },
                    "nested3": []
                }
            },
            "prop2": "string",
            "prop3": [],
            "prop4": null
        }';

        $expected = '{
            "prop1": {
                "nested1": {
                    "nested2": {
                        "inner1": ["hidden"]
                    }
                }
            },
            "prop2": "string",
            "prop4": null
        }';

        $data = $this->fromJson($data);
        $expected = $this->fromJson($expected);

        $result = Utils::dataPrune($data);
        $this->assertEquals($expected, $result);
    }
}
