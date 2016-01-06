<?php

namespace JsonWorks\Tests\Document;

use \JohnStevenson\JsonWorks\Utils;

class ToJsonTest extends \JsonWorks\Tests\Base
{
    protected function getFileExpected($test)
    {
        $filename = __DIR__.'/Fixtures/'.$test.'.json';
        return $this->getFileExpectedJson($filename);
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

    public function testPathWithSlash()
    {
        $schema = null;

        $data = '{
            "prop1": "path/to/somewhere"
        }';

        $expected = '{"prop1":"path/to/somewhere"}';

        $document = $this->getDocument($schema, $data);

        $json = $document->toJson(false);
        $this->assertEquals($expected, $json);
    }

    public function testEscapeUnicode()
    {
        $schema = null;

        $data = '{
            "prop1": "\\u018c"
        }';

        $expected = '{"prop1":"ƌ"}';

        $document = $this->getDocument($schema, $data);

        $json = $document->toJson(false);
        $this->assertEquals($expected, $json);
    }

    public function testTidyNoSchema()
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

    public function testNoTidySchema()
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

    public function testTidySchemaFail()
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

    public function testTidyNestedNoSchema()
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

    public function testNoTidyNestedSchema()
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

    public function testTidyNestedSchemaFail()
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

    public function testPretty()
    {
        $schema = null;

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
        $document = $this->getDocument($schema, $data);

        $json = $document->toJson(true);
        $this->assertEquals($expected, $json);
    }
}
