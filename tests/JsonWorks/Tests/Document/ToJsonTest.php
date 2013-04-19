<?php

namespace JsonWorks\Tests\Document;

use \JohnStevenson\JsonWorks\Utils;

class ToJsonTest extends \JsonWorks\Tests\Base
{
    protected function getExpectedJson($expected)
    {
        return json_encode(json_decode($expected));
    }

    public function testNoData()
    {
        $schema = null;
        $data = null;
        $expected = null;

        $document = $this->getDocument($schema, $data);
        $document->tidy();

        $json = $document->toJson(false);
        $this->assertTrue($document->validate());
        $this->assertEquals($this->getExpectedJson($expected), $json);
    }

    public function testPruneNoSchema()
    {
        $schema = null;

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

        $document = $this->getDocument($schema, $data);
        $document->tidy();

        $json = $document->toJson(false);
        $this->assertTrue($document->validate());
        $this->assertEquals($this->getExpectedJson($expected), $json);
    }

    public function testNoPruneSchema()
    {
        $schema = '{
            "required": ["prop2"]
        }';

        $data = '{
            "prop1": "",
            "prop2": {},
            "prop3": [],
            "prop4": null
        }';

        $expected = $data;

        $document = $this->getDocument($schema, $data);

        $json = $document->toJson(false);
        $this->assertTrue($document->validate());
        $this->assertEquals($this->getExpectedJson($expected), $json);
    }

    public function testPruneSchemaFail()
    {
        $schema = '{
            "required": ["prop2"]
        }';

        $data = '{
            "prop1": "",
            "prop2": {},
            "prop3": [],
            "prop4": null
        }';

        $document = $this->getDocument($schema, $data);
        $document->tidy();

        $this->assertFalse($document->validate());
    }

    public function testPruneNestedNoSchema()
    {
        $schema = null;

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

        $document = $this->getDocument($schema, $data);
        $document->tidy();

        $json = $document->toJson(false);
        $this->assertTrue($document->validate());
        $this->assertEquals($this->getExpectedJson($expected), $json);
    }

    public function testNoPruneNestedSchema()
    {
        $schema = '{
            "properties": {
                "prop1": {
                    "properties": {
                        "nested1": {
                            "required": ["nested3"]
                        }
                    }
                }
            }
        }';

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

        $expected = $data;

        $document = $this->getDocument($schema, $data);

        $json = $document->toJson(false);
        $this->assertTrue($document->validate());
        $this->assertEquals($this->getExpectedJson($expected), $json);
    }

    public function testPruneNestedSchemaFail()
    {
        $schema = '{
            "properties": {
                "prop1": {
                    "properties": {
                        "nested1": {
                            "required": ["nested3"]
                        }
                    }
                }
            }
        }';

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

        $document = $this->getDocument($schema, $data);
        $document->tidy();
        $this->assertFalse($document->validate());
    }
}
