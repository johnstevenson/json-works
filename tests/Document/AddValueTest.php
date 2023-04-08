<?php declare(strict_types=1);

namespace JsonWorks\Tests\Document;

class AddValueTest extends \JsonWorks\Tests\Base
{
    public function testNewArrayValuePush(): void
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
        self::assertTrue($document->addValue($path, $value));
        self::assertEquals(json_decode($expected), $document->getData());
    }

    public function testNewArrayValueIndex(): void
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
        self::assertTrue($document->addValue($path, $value));
        self::assertEquals(json_decode($expected), $document->getData());
    }

    public function testNewArrayValueIndexInvalid1(): void
    {
        $schema = null;

        $data = '{
            "collection": []
        }';

        $document = $this->getDocument($schema, $data);
        $path = '/collection/prop1/-';
        $value = 4;
        self::assertFalse($document->addValue($path, $value));
    }

    public function testNewArrayValueIndexInvalid2(): void
    {
        $schema = null;

        $data = '{
            "collection": [1, [1, 2, 3], 2, 3]
        }';

        $document = $this->getDocument($schema, $data);
        $path = '/collection/1/4';
        $value = 4;
        self::assertFalse($document->addValue($path, $value));
    }

    public function testReplaceArrayValueInserts(): void
    {
        $schema = null;

        $data = '{
            "collection": [0, 1, 2, 3]
        }';

        $expected = '{
            "collection": [0, 1, 2, 2, 3]
        }';

        $document = $this->getDocument($schema, $data);
        $path = '/collection/2';
        $value = 2;
        self::assertTrue($document->addValue($path, $value));
        self::assertEquals(json_decode($expected), $document->getData());
    }

    public function testNewValue(): void
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
        self::assertTrue($document->addValue($path, $value));
        self::assertEquals(json_decode($expected), $document->getData());
    }

    public function testReplaceObjectValue(): void
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
        self::assertTrue($document->addValue($path, $value));
        self::assertEquals(json_decode($expected), $document->getData());
    }

    public function testReplaceObjectNullValue(): void
    {
        $schema = null;

        $data = '{
            "prop1": {
                "inner1": {
                    "inner2": {
                        "prop1": "string",
                        "prop2": null
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
        self::assertTrue($document->addValue($path, $value));
        self::assertEquals(json_decode($expected), $document->getData());
    }

    public function testObjectWithNumericKey(): void
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
        self::assertTrue($document->addValue($path, $value), $msg);
        self::assertEquals(json_decode($expectedArray), $document->getData(), $msg);

        $data = '{"prop1": {}}';
        $document = $this->getDocument($schema, $data);

        $msg = 'Testing that the value is added to the object';
        self::assertTrue($document->addValue($path, $value), $msg);
        self::assertEquals(json_decode($expectedObject), $document->getData(), $msg);
    }

    public function testObjectWithPushKey(): void
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
        self::assertTrue($document->addValue($path, $value), $msg);
        self::assertEquals(json_decode($expectedArray), $document->getData(), $msg);

        $data = '{"prop1": {}}';
        $document = $this->getDocument($schema, $data);

        $msg = 'Testing that the value is added to the object';
        self::assertTrue($document->addValue($path, $value), $msg);
        self::assertEquals(json_decode($expectedObject), $document->getData(), $msg);
    }

    public function testBuildObjectInArray(): void
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
        self::assertTrue($document->addValue($path, $value));

        $path = '/collection/1/lastName';
        $value = 'Smith';
        self::assertTrue($document->addValue($path, $value));

        $path = '/collection/1/age';
        $value = 24;
        self::assertTrue($document->addValue($path, $value));

        self::assertEquals(json_decode($expected), $document->getData());
    }
}
