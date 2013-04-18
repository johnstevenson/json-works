<?php

namespace JsonWorks\Tests\Document;

use \JohnStevenson\JsonWorks\Utils as Utils;

class PruneDataTest extends \JsonWorks\Tests\Base
{
    public function testSimpleNoData()
    {
        $schema = null;
        $data = null;
        $expected = null;

        $document = $this->getDocument($schema, $data);
        $result = $this->callMethod($document, 'pruneData');
        $this->assertTrue($result);
        $this->assertEquals(json_decode($expected), $document->data);
    }

    public function testSimpleNoSchema()
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
        $result = $this->callMethod($document, 'pruneData');
        $this->assertTrue($result);
        $this->assertEquals(json_decode($expected), $document->data);
    }

    public function testSimpleSchemaFail()
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
        $result = $this->callMethod($document, 'pruneData');
        $this->assertFalse($result);
        $this->assertEquals(json_decode($expected), $document->data);
    }

    public function testNestedNoSchema()
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
        $result = $this->callMethod($document, 'pruneData');
        $this->assertTrue($result);
        $this->assertEquals(json_decode($expected), $document->data);
    }

    public function testNestedSchemaFail()
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
        $result = $this->callMethod($document, 'pruneData');
        $this->assertFalse($result);
        $this->assertEquals(json_decode($expected), $document->data);
    }
}
