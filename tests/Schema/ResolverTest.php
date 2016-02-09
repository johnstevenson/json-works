<?php

namespace JsonWorks\Tests\Schema;

class ResolverTest extends \JsonWorks\Tests\Base
{
    public function testRoot()
    {
        $schema = '{
            "properties": {
                "prop1": {"$ref": "#"}
            },
            "additionalProperties": false
        }';

        $data = '{"prop1": {"prop1": 7}}';

        $this->assertTrue($this->validate($schema, $data), 'Testing success');

        $data = '{"prop1": {"prop2": 7}}';
        $this->assertFalse($this->validate($schema, $data), 'Testing failure');
    }

    public function testSingle()
    {
        $schema = '{
            "type" : "object",
            "properties":
            {
                "prop1": {"$ref": "#/definitions/alphanum"},
                "prop2": {"$ref": "#/definitions/alphanum"}
            },
            "definitions":
            {
                "alphanum": {
                    "oneOf": [ {"type": "string"}, {"type": "number"} ]
                }
            }
        }';

        $data = json_decode('{
            "prop1": 7,
            "prop2": "seven"
        }');

        $this->assertTrue($this->validate($schema, $data), 'Testing success');

        $data->prop1 = true;
        $this->assertFalse($this->validate($schema, $data), 'Testing failure');
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

        $data = json_decode('{
            "prop1": 7
        }');

        $this->assertTrue($this->validate($schema, $data), 'Testing success');

        $data->prop1 = true;
        $this->assertFalse($this->validate($schema, $data), 'Testing failure');
    }

    public function testInvalidRefType()
    {
        $schema = '{
            "type" : "object",
            "properties":
            {
                "prop1": {"$ref": 8},
                "prop2": {}
            }
        }';

        $data = '{
            "prop1": 7
        }';

        $this->setExpectedException('RuntimeException', 'Invalid schema value');
        $this->validate($schema, $data);
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

        $data = '{
            "prop1": 7
        }';

        $this->setExpectedException('RuntimeException', 'Unable to find $ref');
        $this->validate($schema, $data);
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

        $data = json_decode('{
            "prop1": 5
        }');

        $this->assertTrue($this->validate($schema, $data), 'Testing success');

        $data->prop1 = 6;
        $this->assertFalse($this->validate($schema, $data), 'Testing failure');
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

        $data = '{
            "prop1": 7
        }';

        $this->setExpectedException('RuntimeException', 'Circular reference');
        $this->validate($schema, $data);
    }

    public function testCompound()
    {
        $schema = '{
            "type" : "object",
            "properties": {
                "prop1": {"$ref": "#/definitions/prop"},
                "prop2": {"$ref": "#/properties/prop1"}
            },
            "itemSchema": [
                {"item0": {}},
                {"$ref": "#/definitions/proparray/1"}
            ],
            "definitions": {
                "alphanum": {
                    "oneOf": [ {"type": "string"}, {"type": "number"} ]
                },
                "prop": {
                    "$ref": "#/definitions/propnext"
                },
                "propnext": {
                    "$ref": "#/itemSchema/1"
                },
                "proparray": [
                    {"item0": {}},
                    {"$ref": "#/definitions/alphanum"}
                ]
            }
        }';

        $data = json_decode('{
            "prop1": 7,
            "prop2": "seven"
        }');

        $this->assertTrue($this->validate($schema, $data), 'Testing success');

        $data->prop1 = true;
        $this->assertFalse($this->validate($schema, $data), 'Testing failure');
    }

    public function testCompoundCircular()
    {
        $schema = '{
            "type" : "object",
            "properties": {
                "prop1": {"$ref": "#/definitions/prop"},
                "prop2": {"$ref": "#/properties/prop1"}
            },
            "itemSchema": [
                {"item0": {}},
                {"$ref": "#/definitions/proparray/1"}
            ],
            "definitions": {
                "prop": {
                    "$ref": "#/definitions/propnext"
                },
                "propnext": {
                    "$ref": "#/itemSchema/1"
                },
                "proparray": [
                    {"item0": {}},
                    {"$ref": "#/properties/prop2"}
                ]
            }
        }';

        $data = '{
            "prop1": 7
        }';

        $this->setExpectedException('RuntimeException', 'Circular reference');
        $this->validate($schema, $data);
    }
}
