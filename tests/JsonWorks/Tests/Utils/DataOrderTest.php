<?php

namespace JsonWorks\Tests\Utils;

use \JohnStevenson\JsonWorks\Utils;

class DataOrderTest extends \JsonWorks\Tests\Base
{
    public function testNoData()
    {
        $schema = '';
        $data = '';
        $expected = '';

        $result = Utils::dataOrder(json_decode($data), $schema);
        $this->assertEquals(json_decode($expected), $result);
    }

    public function testNoSchema()
    {
        $schema = null;

        $data = '{
            "prop4": "",
            "prop2": {},
            "prop1": [],
            "prop3": null
        }';

        $expected = '{
            "prop4": "",
            "prop2": {},
            "prop1": [],
            "prop3": null
        }';

        $schema = $this->getSchemaObject($schema);
        $data = $this->fromJson($data);
        $expected = $this->fromJson($expected);

        $result = Utils::dataOrder($data, $schema);
        $this->assertEquals($expected, $result);
    }


    public function testObjectSimple()
    {
        $schema = '{
            "properties": {
                "prop1": {},
                "prop2": {},
                "prop3": {},
                "prop4": {}
            }
        }';

        $data = '{
            "prop4": null,
            "prop5": 5,
            "prop2": {},
            "prop1": "",
            "prop3": []
        }';

        $expected = '{
            "prop1": "",
            "prop2": {},
            "prop3": [],
            "prop4": null,
            "prop5": 5
        }';

        $schema = $this->getSchemaObject($schema);
        $data = $this->fromJson($data);
        $expected = $this->fromJson($expected);

        $result = Utils::dataOrder($data, $schema);
        $this->assertEquals($expected, $result);
    }

    public function testArraySimple()
    {
        $schema = '{
            "items": {
                "prop1": {},
                "prop2": {},
                "prop3": {},
                "prop4": {}
            }
        }';

        $data = '[
            {
                "prop4": null,
                "prop2": {},
                "prop1": "",
                "prop3": []
            },
            {
                "prop5": 5,
                "prop1": "",
                "prop4": null,
                "prop3": [],
                "prop2": {}
            }
        ]';

        $expected = '[
            {
                "prop1": "",
                "prop2": {},
                "prop3": [],
                "prop4": null
            },
            {
                "prop1": "",
                "prop2": {},
                "prop3": [],
                "prop4": null,
                "prop5": 5
            }
        ]';

        $schema = $this->getSchemaObject($schema);
        $data = $this->fromJson($data);
        $expected = $this->fromJson($expected);

        $result = Utils::dataOrder($data, $schema);
        $this->assertEquals($expected, $result);
    }

    public function testNested()
    {
        $schema = '{
            "properties": {
                "prop1": {},
                "prop2": {
                    "inner1": {
                        "items": {
                            "$ref": "#/definitions/location"
                        }
                    },
                    "inner2": {}
                },
                "prop3": {},
                "prop4": {}
            },
            "definitions": {
                "location": {
                    "properties": {
                        "lat": {"type": "number"},
                        "lng": {"type": "number"}
                    }
                }
            }
        }';

        $data = '{
            "prop4": null,
            "prop5": 5,
            "prop2": {
                "inner2": 2,
                "inner1": [
                    {"lng": 120, "lat": 50},
                    {"lat": 27, "lng": 3}
                ]
            },
            "prop1": "",
            "prop3": []
        }';

        $expected = '{
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
            "prop5": 5
        }';

        $schema = $this->getSchemaObject($schema);
        $data = $this->fromJson($data);
        $expected = $this->fromJson($expected);

        $result = Utils::dataOrder($data, $schema);
        $this->assertEquals($expected, $result);
    }
}
