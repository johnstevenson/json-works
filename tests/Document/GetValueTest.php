<?php

namespace JsonWorks\Tests\Document;

class GetValueTest extends \JsonWorks\Tests\Base
{
    public function testFromObject()
    {
        $schema = null;

        $data = '{
                "prop1":
                {
                    "prop11": "prop11 value"
                }
        }';

        $document = $this->getDocument($schema, $data);

        $path = '/prop1/prop11';
        $expected = 'prop11 value';
        $this->assertTrue($document->hasValue($path, $value), 'Testing success: '.$path);
        $this->assertEquals($expected, $value);

        $path = '/prop1/prop111';
        $this->assertFalse($document->hasValue($path, $value), 'Testing fail: '.$path);
        $this->assertNull($value);
    }

    public function testFromArray()
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
        $this->assertTrue($document->hasValue($path, $value), 'Testing success: '.$path);
        $this->assertEquals($expected, $value);

        $path = '/0/3';
        $this->assertFalse($document->hasValue($path, $value), 'Testing fail: '.$path);
        $this->assertNull($value);
    }

    public function testFromMixed()
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

        # success tests
        $path = '/prop1/firstName';
        $expected = 'Fred';
        $this->assertTrue($document->hasValue($path, $value), 'Testing success: '.$path);
        $this->assertEquals($expected, $value);

        $path = '/prop2/collection';
        $this->assertTrue($document->hasValue($path, $value), 'Testing success: '.$path);
        $this->assertInternalType('array', $value);

        $path = '/prop2/collection/1/lastName';
        $expected = 'Bloggs';
        $this->assertTrue($document->hasValue($path, $value), 'Testing success: '.$path);
        $this->assertEquals($expected, $value);

        $path = '/prop2/collection/3/1/firstName';
        $expected = 'Harry';
        $this->assertTrue($document->hasValue($path, $value), 'Testing success: '.$path);
        $this->assertEquals($expected, $value);

        # fail tests
        $path = '/prop1/lastName';
        $this->assertFalse($document->hasValue($path, $value), 'Testing fail: '.$path);
        $this->assertNull($value);

        $path = '/prop2/collection/0/lastName';
        $this->assertFalse($document->hasValue($path, $value), 'Testing fail: '.$path);
        $this->assertNull($value);

        $path = '/prop2/collection/3/0/firstName';
        $this->assertFalse($document->hasValue($path, $value), 'Testing fail: '.$path);
        $this->assertNull($value);

    }
}
