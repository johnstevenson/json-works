<?php declare(strict_types=1);

namespace JsonWorks\Tests\Schema;

use JohnStevenson\JsonWorks\Schema\Resolver;

class ResolverTest extends \JsonWorks\Tests\Base
{
    public function testRoot(): void
    {
        $schema = '{
            "properties": {
                "prop1": {"$ref": "#"}
            },
            "additionalProperties": false
        }';

        $data = '{"prop1": {"prop1": 7}}';

        self::assertTrue($this->validate($schema, $data), 'Testing success');

        $data = '{"prop1": {"prop2": 7}}';
        self::assertFalse($this->validate($schema, $data), 'Testing failure');
    }

    public function testSingle(): void
    {
        $schema = '{
            "type": "object",
            "properties": {
                "prop1": {"$ref": "#/definitions/alphanum"},
                "prop2": {"$ref": "#/definitions/alphanum"}
            },
            "definitions": {
                "alphanum": {
                    "oneOf": [ {"type": "string"}, {"type": "number"} ]
                }
            }
        }';

        $data = json_decode('{
            "prop1": 7,
            "prop2": "seven"
        }');

        self::assertTrue($this->validate($schema, $data), 'Testing success');

        $data->prop1 = true;
        self::assertFalse($this->validate($schema, $data), 'Testing failure');
    }

    public function testSingleEncoded(): void
    {
        $schema = '{
            "type": "object",
            "properties": {
                "prop1": {"$ref": "#/definitions/alpha~1num"},
                "prop2": {}
            },
            "definitions": {
                "alpha/num": {
                    "oneOf": [ {"type": "string"}, {"type": "number"} ]
                }
            }
        }';

        $data = json_decode('{
            "prop1": 7
        }');

        self::assertTrue($this->validate($schema, $data), 'Testing success');

        $data->prop1 = true;
        self::assertFalse($this->validate($schema, $data), 'Testing failure');
    }

    public function testInvalidRefType(): void
    {
        $schema = '{
            "type": "object",
            "properties": {
                "prop1": {"$ref": 8},
                "prop2": {}
            }
        }';

        $data = '{
            "prop1": 7
        }';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid schema value');
        $this->validate($schema, $data);
    }

    public function testNotFound(): void
    {
        $schema = '{
            "type": "object",
            "properties": {
                "prop1": {"$ref": "#/definitions/alphanum"},
                "prop2": {}
            }
        }';

        $data = '{
            "prop1": 7
        }';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to find $ref');
        $this->validate($schema, $data);
    }

    public function testInArray(): void
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

        self::assertTrue($this->validate($schema, $data), 'Testing success');

        $data->prop1 = 6;
        self::assertFalse($this->validate($schema, $data), 'Testing failure');
    }

    public function testCircular(): void
    {
        $schema = '{
            "type": "object",
            "properties": {
                "prop1": {"$ref": "#/definitions/alphanum"},
                "prop2": {}
            },
            "definitions": {
                "alphanum": {"$ref": "#/properties/prop1"}
            }
        }';

        $data = '{
            "prop1": 7
        }';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Circular reference');
        $this->validate($schema, $data);
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

        self::assertTrue($this->validate($schema, $data), 'Testing success');

        $data->prop1 = true;
        self::assertFalse($this->validate($schema, $data), 'Testing failure');
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

        $data = '{
            "prop1": 7
        }';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Circular reference');
        $this->validate($schema, $data);
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

        $data = $this->objectFromJson('{
            "list1": [ { "prop1": "string one", "list": [ "one", 1]} ],
            "list2": [ { "prop1": "string two", "list": [ "two", 2]} ]
        }');

        list($result, $error) = $this->validateEx($schema, $data, 'Testing success');
        self::assertTrue($result, $error);

        $data->list1[0]->list = ['one', false];
        list($result, $error) = $this->validateEx($schema, $data, 'Testing failure');
        self::assertFalse($result, $error);
    }

    public function testAnotherCompound(): void
    {
        $schema = '{
            "properties": {
                "a": { "$ref": "#/a" },
                "b": { "$ref": "#/b" },
                "c": {
                    "properties": {
                        "milk": { "$ref": "#/c" }
                    }
                }
            },
            "a": {
                "properties": {
                    "milk": {
                        "enum": ["cow", "goat", "yak"]
                    },
                    "eggs": {
                        "enum": ["bird", "chicken", "goose"]
                    }
                }
            },
            "b": { "$ref": "#/a" },
            "c": { "$ref": "#/b/properties/milk" }
        }';

        $data = '{
            "a": {
                "milk": "cow",
                "eggs": "bird"
            },
            "b": {
                "milk": "goat",
                "eggs": "goose"
            },
            "c": {
                "milk": "yak"
            }
        }';

        $data = $this->objectFromJson($data);

        foreach ($data as $key => $value) {
            $msg = sprintf('Testing success with data property: %s', $key);
            $prop = (object) [$key => $value];

            self::assertTrue($this->validate($schema, $prop), $msg);
        }

        self::assertTrue($this->validate($schema, $data), 'Testing success');
    }

    public function testAnotherCompound2(): void
    {
        $schema = '{
            "properties": {
                "a": {
                    "properties": {
                        "milk": {
                            "enum": ["cow", "goat", "yak"]
                        },
                        "eggs": {
                            "enum": ["bird", "chicken", "goose"]
                        }
                    }
                },
                "b": { "$ref": "#/properties/a" },
                "c": {
                    "properties": {
                        "milk": { "$ref": "#/properties/b/properties/milk" }
                    }
                }
            }
        }';

        $data = '{
            "a": {
                "milk": "cow",
                "eggs": "bird"
            },
            "b": {
                "milk": "goat",
                "eggs": "goose"
            },
            "c": {
                "milk": "yak"
            }
        }';

        $data = $this->objectFromJson($data);

        foreach ($data as $key => $value) {
            $msg = sprintf('Testing success with data property: %s', $key);
            $prop = (object) [$key => $value];

            self::assertTrue($this->validate($schema, $prop), $msg);
        }

        self::assertTrue($this->validate($schema, $data), 'Testing success');
    }

    public function testAnotherCompound3(): void
    {
        $schema = '{
            "properties": {
                "a": {
                    "properties": {
                        "milk": {
                            "enum": ["cow", "goat", "yak"]
                        },
                        "eggs": {
                            "enum": ["bird", "chicken", "goose"]
                        }
                    }
                },
                "b": { "$ref": "#/properties/a" },
                "c": {
                    "properties": {
                        "milk": { "$ref": "#/properties/b/properties/milk" }
                    }
                }
            }
        }';

        $schema = $this->objectFromJson($schema);

        $resolver = new Resolver($schema);

        $ref = '#/properties/a';
        $expected = $schema->properties->a;

        $result = $resolver->getRef($ref);
        self::assertEquals($expected, $result);

        $ref = '#/properties/b/properties/milk';
        $expected = $schema->properties->a->properties->milk;

        $result = $resolver->getRef($ref);
        self::assertEquals($expected, $result);
    }
}
