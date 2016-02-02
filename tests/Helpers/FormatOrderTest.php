<?php

namespace JsonWorks\Tests\Helpers;

use JohnStevenson\JsonWorks\Helpers\Formatter;

class FormatOrderTest extends \JsonWorks\Tests\Base
{
    protected $formatter;

    protected function setUp()
    {
        $this->formatter = new Formatter();
    }

    public function testNoData()
    {
        $schema = $this->getSchema(null);
        $data = '';
        $expected = '';

        $result = $this->formatter->order(json_decode($data), $schema);
        $this->assertEquals(json_decode($expected), $result);
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

        $schema = $this->getSchema($schema);
        $data = $this->fromJson($data);
        $expected = $this->getExpectedJson($expected);

        $msg = 'Testing with schema';
        $result = json_encode($this->formatter->order($data, $schema));
        $this->assertEquals($expected, $result, $msg);

        $msg = 'Testing no schema';
        $result = json_encode($this->formatter->order($data, null));
        $this->assertEquals($expected, $result, $msg);
    }

    public function testArraySimple()
    {
        $schema = '{
            "items": {
                "properties": {
                    "prop1": {},
                    "prop2": {},
                    "prop3": {},
                    "prop4": {}
                }
            }
        }';

        $data = '[{
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
        }]';

        $expected = '[{
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
        }]';

        $schema = $this->getSchema($schema);
        $data = $this->fromJson($data);
        $expected = $this->getExpectedJson($expected);

        $msg = 'Testing with schema';
        $result = json_encode($this->formatter->order($data, $schema));
        $this->assertEquals($expected, $result, $msg);

        $msg = 'Testing no schema';
        $result = json_encode($this->formatter->order($data, null));
        $this->assertEquals($expected, $result, $msg);
    }

    public function testNested()
    {
        $schema = '{
            "properties": {
                "prop1": {},
                "prop2": {
                    "properties": {
                        "inner1": {
                            "items": {
                                "$ref": "#/definitions/location"
                            }
                        },
                        "inner2": {}
                    }
                },
                "prop3": {
                    "items": {
                        "$ref": "#/definitions/name"
                    }
                },
                "prop4": {}
            },
            "definitions": {
                "location": {
                    "properties": {
                        "lat": {"type": "number"},
                        "lng": {"type": "number"}
                    }
                },
                "name": {
                    "properties": {
                        "firstName": {"type": "string"},
                        "lastName": {"type": "string"}
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
            "prop3": [
                {"lastName": "Bloggs", "firstName": "Fred"},
                {"lastName": "Smith", "firstName": "John"},
                5
            ]
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
            "prop3": [
                {"firstName": "Fred", "lastName": "Bloggs"},
                {"firstName": "John", "lastName": "Smith"},
                5
            ],
            "prop4": null,
            "prop5": 5
        }';

        $schema = $this->getSchema($schema);
        $data = $this->fromJson($data);
        $expected = $this->getExpectedJson($expected);

        $msg = 'Testing with schema';
        $result = json_encode($this->formatter->order($data, $schema));
        $this->assertEquals($expected, $result, $msg);

        $msg = 'Testing no schema';
        $result = json_encode($this->formatter->order($data, null));
        $this->assertEquals($expected, $result, $msg);
    }
}
