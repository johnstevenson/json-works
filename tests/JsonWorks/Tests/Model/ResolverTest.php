<?php

namespace JsonWorks\Tests\Model;

class ResolverTest extends \JsonWorks\Tests\Base
{
    public function testSingle()
    {
        $schema = '{
            "type" : "object",
            "properties":
            {
                "prop1": {"$ref": "#/definitions/alphanum"},
                "prop2": {}
            },
            "definitions":
            {
                "alphanum": {
                    "oneOf": [ {"type": "string"}, {"type": "number"} ]
                }
            }
        }';

        $data = null;

        $document = $this->getDocument($schema, $data);
        $expected = $document->schema->data->definitions->alphanum;
        $value = $document->schema->data->properties->prop1;

        $this->assertEquals($expected, $value);
    }

    public function testSingleEncoded()
    {
        $schema = '{
            "type" : "object",
            "properties":
            {
                "prop1": {"$ref": "#/definitions/alpha~1num"},
                "prop2": {}
            },
            "definitions":
            {
                "alpha/num": {
                    "oneOf": [ {"type": "string"}, {"type": "number"} ]
                }
            }
        }';

        $data = null;

        $document = $this->getDocument($schema, $data);
        $expected = $document->schema->data->definitions->{'alpha/num'};
        $value = $document->schema->data->properties->prop1;

        $this->assertEquals($expected, $value);
    }

    public function testNotFound()
    {
        $schema = '{
            "type" : "object",
            "properties":
            {
                "prop1": {"$ref": "#/definitions/alphanum"},
                "prop2": {}
            }
        }';

        $data = null;

        $this->setExpectedException('RuntimeException');
        $this->getDocument($schema, $data);
    }

    public function testCircular()
    {
        $schema = '{
            "type" : "object",
            "properties":
            {
                "prop1": {"$ref": "#/definitions/alphanum"},
                "prop2": {}
            },
            "definitions":
            {
                "alphanum": {"$ref": "#/properties/prop1"}
            }
        }';

        $data = null;

        $this->setExpectedException('RuntimeException');
        $this->getDocument($schema, $data);
    }

    public function testCompound()
    {
        $schema = '{
            "type" : "object",
            "properties":
            {
                "prop1": {"$ref": "#/definitions/prop"},
                "prop2": {"$ref": "#/properties/prop1"}
            },
            "items": [
                "item0",
                {"$ref": "#/definitions/proparray/1"}
            ],
            "definitions":
            {
                "alphanum": {
                    "oneOf": [ {"type": "string"}, {"type": "number"} ]
                },
                "prop": {
                    "$ref": "#/definitions/propnext"
                },
                "propnext": {
                    "$ref": "#/items/1"
                },
                "proparray":
                [
                 "item0",
                 {"$ref": "#/definitions/alphanum"}
                ]
            }
        }';

        $data = null;

        $document = $this->getDocument($schema, $data);
        $expected = $document->schema->data->definitions->alphanum;
        $value = $document->schema->data->properties->prop2;

        $this->assertEquals($expected, $value);
    }

    public function testCompoundCircular()
    {
        $schema = '{
            "type" : "object",
            "properties":
            {
                "prop1": {"$ref": "#/definitions/prop"},
                "prop2": {"$ref": "#/properties/prop1"}
            },
            "items": [
                "item0",
                {"$ref": "#/definitions/proparray/1"}
            ],
            "definitions":
            {
                "prop": {
                    "$ref": "#/definitions/propnext"
                },
                "propnext": {
                    "$ref": "#/items/1"
                },
                "proparray":
                [
                 "item0",
                 {"$ref": "#/properties/prop2"}
                ]
            }
        }';

        $data = null;

        $this->setExpectedException('RuntimeException');
        $this->getDocument($schema, $data);
    }
}
