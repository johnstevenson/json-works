<?php

namespace JsonWorks\Tests\Document;

class GetValueTest extends \JsonWorks\Tests\Base
{
    public function testObjectValid()
    {
        $schema = null;

        $data = '{
                "prop1":
                {
                    "prop11": {
                        "prop111": {
                            "prop1111": "prop1111 value"
                        }
                    }
                }
        }';

        $document = $this->getDocument($schema, $data);

        $path = '/prop1/prop11/prop111/prop1111';
        $expected = 'prop1111 value';
        $this->assertTrue($document->getValue($path, $value));
        $this->assertEquals($expected, $value);
    }

    public function testObjectInvalid()
    {
        $schema = null;

        $data = '{
                "prop1": { "prop11": {} }
        }';

        $document = $this->getDocument($schema, $data);

        $path = '/prop1/prop111/prop1111';
        $this->assertFalse($document->getValue($path, $value));
        $this->assertNull($value);
    }

    public function testArrayValid()
    {
        $schema = null;

        $data = '[
                "item0",
                [
                    "item10",
                    [0, 1, "item112 value"]
                ]
        ]';

        $document = $this->getDocument($schema, $data);

        $path = '/1/1/2';
        $expected = 'item112 value';
        $this->assertTrue($document->getValue($path, $value));
        $this->assertEquals($expected, $value);
    }

    public function testArrayInvalid()
    {
        $schema = null;

        $data = '[
                [1, 2, 3]
        ]';

        $document = $this->getDocument($schema, $data);

        $path = '/0/3';
        $this->assertFalse($document->getValue($path, $value));
        $this->assertNull($value);
    }

    public function testComplexValid()
    {
        $schema = null;

        $data = '{
                "prop1":
                {
                    "firstName": "Fred"
                },
                "prop2":
                {
                    "collection":
                    [
                        "item0",
                        {"firstName": "Fred", "lastName": "Bloggs"},
                        "item2",
                        [false, {"firstName": "Harry", "lastName": "Smith"}]
                    ]
                }
        }';

        $document = $this->getDocument($schema, $data);

        $path = '/prop1/firstName';
        $expected = 'Fred';
        $this->assertTrue($document->getValue($path, $value), '/prop1/firstName');
        $this->assertEquals($expected, $value);

        $path = '/prop2/collection';
        $this->assertTrue($document->getValue($path, $value), '/prop2/collection');
        $this->assertInternalType('array', $value);

        $path = '/prop2/collection/1/lastName';
        $expected = 'Bloggs';
        $this->assertTrue($document->getValue($path, $value), '/prop2/collection/1/lastName');
        $this->assertEquals($expected, $value);

        $path = '/prop2/collection/3/1/firstName';
        $expected = 'Harry';
        $this->assertTrue($document->getValue($path, $value), '/prop2/collection/3/1/firstName');
        $this->assertEquals($expected, $value);

    }
}
