<?php

namespace JsonWorks\Tests\Helpers;

use JohnStevenson\JsonWorks\Helpers\FormatManager;

class FormatterOrderDataTest extends \JsonWorks\Tests\Base
{
    protected $formatter;

    protected function setUp()
    {
        $this->formatter = new FormatManager();
    }

    public function testNoData()
    {
        $schema = '';
        $data = '';
        $expected = '';

        $result = $this->formatter->order(json_decode($data), $schema);
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

        $schema = $this->getSchema($schema);
        $data = $this->fromJson($data);
        $expected = $this->getExpectedJson($expected);

        $result = json_encode($this->formatter->order($data, $schema));
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

        $schema = $this->getSchema($schema);
        $data = $this->fromJson($data);
        $expected = $this->getExpectedJson($expected);

        $result = json_encode($this->formatter->order($data, $schema));
        $this->assertEquals($expected, $result);
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

        $result = json_encode($this->formatter->order($data, $schema));
        $this->assertEquals($expected, $result);
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
                    "items": [{
                        "properties": {
                            "name": {
                                "$ref": "#/definitions/name"
                            }
                        }
                    },
                    {
                        "properties": {
                            "location": {
                                "$ref": "#/definitions/location"
                            }
                        }
                    }]
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
            "prop3": [{
                "location": {"lng": 120, "lat": 50}
            },
            {
                "name": {"lastName": "Bloggs", "firstName": "Fred"}
            },
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
            "prop3": [{
                "name": {"firstName": "Fred", "lastName": "Bloggs"}
            },
            {
                "location": {"lat": 50, "lng": 120}
            },
            5
            ],
            "prop4": null,
            "prop5": 5
        }';

        $schema = $this->getSchema($schema);
        $data = $this->fromJson($data);
        $expected = $this->getExpectedJson($expected);

        $result = json_encode($this->formatter->order($data, $schema));
        $this->assertEquals($expected, $result);
    }
}
