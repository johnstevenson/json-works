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

    public function testObjectWithNumericKey()
    {
        $schema = null;

        $expectedArray = '{
            "prop1": ["myValue"]
        }';

        $expectedObject = '{
            "prop1": {"0": "myValue"}
        }';

        $path = '/prop1/0';
        $value = 'myValue';

        $document = $this->getDocument(null, null);

        $msg = 'Testing that an array is created';
        $this->assertTrue($document->addValue($path, $value), $msg);
        $this->assertEquals(json_decode($expectedArray), $document->data, $msg);

        $data = '{"prop1": {}}';
        $document = $this->getDocument($schema, $data);

        $msg = 'Testing that the value is added to the object';
        $this->assertTrue($document->addValue($path, $value), $msg);
        $this->assertEquals(json_decode($expectedObject), $document->data, $msg);
    }

    public function testObjectWithPushKey()
    {
        $schema = null;

        $expectedArray = '{
            "prop1": ["myValue"]
        }';

        $expectedObject = '{
            "prop1": {"-": "myValue"}
        }';

        $path = '/prop1/-';
        $value = 'myValue';

        $document = $this->getDocument(null, null);

        $msg = 'Testing that an array is created';
        $this->assertTrue($document->addValue($path, $value), $msg);
        $this->assertEquals(json_decode($expectedArray), $document->data, $msg);

        $data = '{"prop1": {}}';
        $document = $this->getDocument($schema, $data);

        $msg = 'Testing that the value is added to the object';
        $this->assertTrue($document->addValue($path, $value), $msg);
        $this->assertEquals(json_decode($expectedObject), $document->data, $msg);
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
        $value = ['firstName' => 'John'];
        $this->assertTrue($document->addValue($path, $value));

        $path = '/collection/1/lastName';
        $value = 'Smith';
        $this->assertTrue($document->addValue($path, $value));

        $path = '/collection/1/age';
        $value = 24;
        $this->assertTrue($document->addValue($path, $value));

        $this->assertEquals(json_decode($expected), $document->data);
    }
}
