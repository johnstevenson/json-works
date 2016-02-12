<?php

namespace JsonWorks\Tests\Schema;

use JohnStevenson\JsonWorks\Schema\Cache;

class CacheTest extends \JsonWorks\Tests\Base
{
    protected function resolve($ref, $schema)
    {
        if (!is_object($schema)) {
            throw new \InvalidArgumentException('Test not run, schema is not an object');
        }
        $cache = new Cache($schema);

        return $cache->resolveRef($ref, $schema);
    }

    public function testRoot()
    {
        $schema = '{
            "properties": {
                "prop1": {"$ref": "#"}
            },
            "additionalProperties": false
        }';

        $schema = $this->fromJson($schema);
        $expected = $schema;

        $ref = '#';
        $result = $this->resolve($ref, $schema);

        $this->assertEquals($expected, $result);
    }

    public function testRootCircular()
    {
        $schema = '{ "$ref": "#" }';

        $schema = $this->fromJson($schema);
        $this->setExpectedException('RuntimeException', 'Circular reference');

        $ref = '#';
        $this->resolve($ref, $schema);
    }

    public function testSimple()
    {
        $schema = '{
            "type": "object",
            "properties": {
                "prop1": {"$ref": "#/definitions/alphanum"}
            },
            "definitions": {
                "alphanum": {
                    "oneOf": [ {"type": "string"}, {"type": "number"} ]
                }
            }
        }';

        $schema = $this->fromJson($schema);
        $expected = $schema->definitions->alphanum;

        $ref = '#/definitions/alphanum';
        $result = $this->resolve($ref, $schema);

        $this->assertEquals($expected, $result);
    }

    public function testSingleEncoded()
    {
        $schema = '{
            "type": "object",
            "properties": {
                "prop1": {"$ref": "#/definitions/alpha~1num"}
            },
            "definitions": {
                "alpha/num": {
                    "oneOf": [ {"type": "string"}, {"type": "number"} ]
                }
            }
        }';

        $schema = $this->fromJson($schema);
        $expected = $schema->definitions->{'alpha/num'};

        $ref = '#/definitions/alpha~1num';
        $result = $this->resolve($ref, $schema);

        $this->assertEquals($expected, $result);
    }

    public function testNotFound()
    {
        $schema = '{
            "type": "object",
            "properties": {
                "prop1": {"$ref": "#/definitions/alphanum"}
             }
        }';

        $schema = $this->fromJson($schema);
        $this->setExpectedException('RuntimeException', 'Unable to find $ref');

        $ref = '#/definitions/alphanum';
        $this->resolve($ref, $schema);
    }

    public function testCircular()
    {
        $schema = '{
            "type": "object",
            "properties": {
                "prop1": {"$ref": "#/definitions/alphanum"}
            },
            "definitions": {
                "alphanum": {"$ref": "#/properties/prop1"}
            }
        }';

        $schema = $this->fromJson($schema);
        $this->setExpectedException('RuntimeException', 'Circular reference');

        $ref = '#/definitions/alphanum';
        $this->resolve($ref, $schema);
    }

    public function testCompound()
    {
        $schema = '{
            "type": "object",
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

        $schema = $this->fromJson($schema);
        $expected = $schema->definitions->alphanum;

        $ref = '#/properties/prop1';
        $result = $this->resolve($ref, $schema);

        $this->assertEquals($expected, $result);
    }

    public function testCompoundCircular()
    {
        $schema = '{
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

        $schema = $this->fromJson($schema);
        $this->setExpectedException('RuntimeException', 'Circular reference');

        $ref = '#/properties/prop1';
        $this->resolve($ref, $schema);
    }

    public function testReffedProperty()
    {
        $schema = '{
            "a": {
                "a1": {
                    "type": "number"
                },
                "a2": {
                    "type": "boolean"
                }
            },
            "b": { "$ref": "#/a" },
            "c": { "$ref": "#/b/a1" }
        }';

        $schema = $this->fromJson($schema);

        // Tests that #/b/a1 finds the parent #/a, resolves it and adds it
        // to #b, then searches #b for /a1
        $expected = $schema->a->a1;

        $ref = '#/b/a1';
        $result = $this->resolve($ref, $schema);

        $this->assertEquals($expected, $result);
    }

    public function testReffedItem()
    {
        $schema = '{
            "a": [ "number", "boolean" ],
            "b": { "$ref": "#/a" },
            "c": { "$ref": "#/b/1" }
        }';

        $schema = $this->fromJson($schema);
        $expected = $schema->a[1];

        // Tests that #/b/1 finds the parent #/a, resolves it and adds it
        // to #b, then searches #b for /1
        $ref = '#/b/1';

        $result = $this->resolve($ref, $schema);
        $this->assertEquals($expected, $result);
    }
}
