<?php declare(strict_types=1);

namespace JsonWorks\Tests\Schema;

use \stdClass;

use JohnStevenson\JsonWorks\Schema\Cache;

class CacheTest extends \JsonWorks\Tests\Base
{
    /**
     * @return mixed
     */
    protected function resolve(string $ref, stdClass $schema)
    {
        $cache = new Cache($schema);

        return $cache->resolveRef($ref);
    }

    public function testRoot(): void
    {
        $schema = '{
            "properties": {
                "prop1": {"$ref": "#"}
            },
            "additionalProperties": false
        }';

        $schema = $this->objectFromJson($schema);
        $expected = $schema;

        $ref = '#';
        $result = $this->resolve($ref, $schema);

        self::assertEquals($expected, $result);
    }

    public function testRootCircular(): void
    {
        $schema = '{ "$ref": "#" }';

        $schema = $this->objectFromJson($schema);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Circular reference');

        $ref = '#';
        $this->resolve($ref, $schema);
    }

    public function testSimple(): void
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

        $schema = $this->objectFromJson($schema);
        $expected = $schema->definitions->alphanum;

        $ref = '#/definitions/alphanum';
        $result = $this->resolve($ref, $schema);

        self::assertEquals($expected, $result);
    }

    public function testSingleEncoded(): void
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

        $schema = $this->objectFromJson($schema);
        // @phpstan-ignore-next-line
        $expected = $schema->definitions->{'alpha/num'};

        $ref = '#/definitions/alpha~1num';
        $result = $this->resolve($ref, $schema);

        self::assertEquals($expected, $result);
    }

    public function testNotFound(): void
    {
        $schema = '{
            "type": "object",
            "properties": {
                "prop1": {"$ref": "#/definitions/alphanum"}
             }
        }';

        $schema = $this->objectFromJson($schema);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to find $ref');

        $ref = '#/definitions/alphanum';
        $this->resolve($ref, $schema);
    }

    public function testCircular(): void
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

        $schema = $this->objectFromJson($schema);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Circular reference');

        $ref = '#/definitions/alphanum';
        $this->resolve($ref, $schema);
    }

    public function testCompound(): void
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
                "prop": {
                    "$ref": "#/definitions/propnext"
                },
                "propnext": {
                    "$ref": "#/itemSchema/1"
                },
                "proparray": [
                    {"item0": {}},
                    {"$ref": "#/definitions/alphanum"}
                ],
                "alphanum": {
                    "oneOf": [ {"type": "string"}, {"type": "number"} ]
                }
            }
        }';

        $schema = $this->objectFromJson($schema);
        $expected = $schema->definitions->alphanum;

        $ref = '#/properties/prop1';
        $result = $this->resolve($ref, $schema);
        self::assertEquals($expected, $result);

        $ref = '#/properties/prop2';
        $result = $this->resolve($ref, $schema);
        self::assertEquals($expected, $result);
    }

    public function testCompoundCircular(): void
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

        $schema = $this->objectFromJson($schema);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Circular reference');

        $ref = '#/properties/prop1';
        $this->resolve($ref, $schema);
    }

    public function testCompoundSimilarNames(): void
    {
        $schema = '{
            "type": "object",
            "properties": {
                "list1": {
                    "type": "array",
                    "items": { "$ref": "#/definitions/list" }
                },
                "list2": { "$ref": "#/properties/list1" }
            },
            "definitions": {
                "list": {
                    "type": "object",
                    "properties": {
                        "prop1": {"type": "string"},
                        "list": {
                            "type": "array",
                            "items": { "$ref": "#/definitions/list-list" }
                        }
                    }
                },
                "list-list": {
                    "$ref": "#/definitions/alphanum"
                },
                "alphanum": {
                    "oneOf": [ {"type": "string"}, {"type": "number"} ]
                }
            }
        }';

        $schema = $this->objectFromJson($schema);
        $expected = $schema->definitions->alphanum;

        $ref = '#/properties/list1/items/properties/list/items';
        $result = $this->resolve($ref, $schema);

        self::assertEquals($expected, $result);

        /*
        This fails on recursions - could be a bug
        $ref = '#/properties/list2/items/properties/list/items';
        $result = $this->resolve($ref, $schema);
        self::assertEquals($expected, $result);
        */
    }

    public function testReffedProperty(): void
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

        $schema = $this->objectFromJson($schema);

        // Tests that #/b/a1 finds the parent #/a, resolves it and adds it
        // to #b, then searches #b for /a1
        $expected = $schema->a->a1;

        $ref = '#/b/a1';
        $result = $this->resolve($ref, $schema);

        self::assertEquals($expected, $result);
    }

    public function testReffedItem(): void
    {
        $schema = '{
            "a": [ "number", "boolean" ],
            "b": { "$ref": "#/a" },
            "c": { "$ref": "#/b/1" }
        }';

        $schema = $this->objectFromJson($schema);
        $expected = $schema->a[1];

        // Tests that #/b/1 finds the parent #/a, resolves it and adds it
        // to #b, then searches #b for /1
        $ref = '#/b/1';

        $result = $this->resolve($ref, $schema);
        self::assertEquals($expected, $result);
    }
}
