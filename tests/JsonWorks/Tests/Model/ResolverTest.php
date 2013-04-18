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

    /**
    * @expectedException        RuntimeException
    * @expectedExceptionMessage Invalid reference
    *
    */
    public function testInvalidRef1()
    {
        $schema = '{
            "type" : "object",
            "properties":
            {
                "prop1": {"$ref": "/definitions/alphanum"},
                "prop2": {}
            }
        }';

        $data = null;
        $this->getDocument($schema, $data);

    }

    /**
    * @expectedException        RuntimeException
    * @expectedExceptionMessage Invalid reference
    *
    */
    public function testInvalidRef2()
    {
        $schema = '{
            "type" : "object",
            "properties":
            {
                "prop1": {"$ref": 8},
                "prop2": {}
            }
        }';

        $data = null;
        $this->getDocument($schema, $data);

    }

    /**
    * @expectedException        RuntimeException
    * @expectedExceptionMessage Unable to find ref
    *
    */
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
        $this->getDocument($schema, $data);
    }

    public function testInArray()
    {
        $schema = '{
            "properties": {
                "prop1": {
                    "allOf": [
                        {"$ref": "#/definitions/def1"},
                        {"$ref": "#/definitions/def2"}
                    ]
                }
            },
            "definitions": {
                "def1": {"type": "integer"},
                "def2": {"maximum": 5}
            }
        }';

        $data = null;

        $document = $this->getDocument($schema, $data);

        $expected = $document->schema->data->definitions->def1;
        $value = $document->schema->data->properties->prop1->allOf[0];
        $this->assertEquals($expected, $value);

        $expected = $document->schema->data->definitions->def2;
        $value = $document->schema->data->properties->prop1->allOf[1];
        $this->assertEquals($expected, $value);

    }

    /**
    * @expectedException        RuntimeException
    * @expectedExceptionMessage Circular reference
    *
    */
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

    /**
    * @expectedException        RuntimeException
    * @expectedExceptionMessage Circular reference
    *
    */
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
        $this->getDocument($schema, $data);
    }
}
