<?php

namespace JsonWorks\Tests\Document;

class AddValueTest extends \JsonWorks\Tests\Base
{
    public function testNewArrayValuePush()
    {
        $schema = null;

        $data = '{
            "collection": [1, [1, 2, 3], 2, 3]
        }';

        $expected = '{
            "collection": [1, [1, 2, 3, 4], 2, 3]
        }';

        $document = $this->getDocument($schema, $data);
        $path = '/collection/1/-';
        $value = 4;
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals(json_decode($expected), $document->data);
        $this->assertEquals(3, $document->lastPushIndex);
    }

    public function testNewArrayValueIndex()
    {
        $schema = null;

        $data = '{
            "collection": [1, [1, 2, 3], 2, 3]
        }';

        $expected = '{
            "collection": [1, [1, 2, 3, 4], 2, 3]
        }';

        $document = $this->getDocument($schema, $data);
        $path = '/collection/1/3';
        $value = 4;
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals(json_decode($expected), $document->data);
    }

    public function testReplaceArrayValue()
    {
        $schema = null;

        $data = '{
            "collection": [1, [1, 2, 3], 2, 3]
        }';

        $expected = '{
            "collection": [1, [1, 2, 2], 2, 3]
        }';

        $document = $this->getDocument($schema, $data);
        $path = '/collection/1/2';
        $value = 2;
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals(json_decode($expected), $document->data);
        $this->assertEquals(0, $document->lastPushIndex);
    }

    public function testNewValue()
    {
        $schema = null;

        $data = '{
            "prop1": {
                "inner1": {
                    "inner2": {
                        "prop1": "string"
                    }
                }
            }
        }';

        $expected = '{
            "prop1": {
                "inner1": {
                    "inner2": {
                        "prop1": "string",
                        "prop2": false
                    }
                }
            }
        }';

        $document = $this->getDocument($schema, $data);
        $path = '/prop1/inner1/inner2/prop2';
        $value = false;
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals(json_decode($expected), $document->data);
    }

    public function testReplaceValue()
    {
        $schema = null;

        $data = '{
            "prop1": {
                "inner1": {
                    "inner2": {
                        "prop1": "string",
                        "prop2": false
                    }
                }
            }
        }';

        $expected = '{
            "prop1": {
                "inner1": {
                    "inner2": {
                        "prop1": "string",
                        "prop2": true
                    }
                }
            }
        }';

        $document = $this->getDocument($schema, $data);
        $path = '/prop1/inner1/inner2/prop2';
        $value = true;
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals(json_decode($expected), $document->data);
    }

    public function testObjectWithArrayPropertyNames()
    {
        $schema = '{
            "type": "object",
            "properties": {
                "prop1": {
                    "type": "object",
                    "patternProperties":
                    {
                        "^[0-9]*$": {"$ref": "#/definitions/alphanum"},
                        "^-{1,1}$": {"$ref": "#/definitions/alphanum"}
                    }
                }
            },
            "definitions":
            {
                "alphanum": {
                    "oneOf": [ {"type": "string"}, {"type": "number"} ]
                }
            }
        }';

        $expected = '{
            "prop1": {
                "0": 1,
                "-": "value"
            }
        }';

        $document = $this->getDocument($schema, null);
        $path = '/prop1';
        $value = json_decode('{"0": 1, "-": "value"}');
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals(json_decode($expected), $document->data);
    }

    public function testBuildObjectInArray()
    {
        $schema = null;

        $data = '{
            "collection": [
                {"firstName": "Fred", "lastName": "Bloggs", "age": 42}
            ]
        }';

        $expected = '{
            "collection": [
                {"firstName": "Fred", "lastName": "Bloggs", "age": 42},
                {"firstName": "John", "lastName": "Smith", "age": 24}
            ]
        }';

        $document = $this->getDocument($schema, $data);
        $path = '/collection/-';
        $value = array('firstName' => 'John');
        $this->assertTrue($document->addValue($path, $value));

        $base = '/collection/'.$document->lastPushIndex;

        $path = $base.'/lastName';
        $value = 'Smith';
        $this->assertTrue($document->addValue($path, $value));

        $path = $base.'/age';
        $value = 24;
        $this->assertTrue($document->addValue($path, $value));

        $this->assertEquals(json_decode($expected), $document->data);
    }

}
