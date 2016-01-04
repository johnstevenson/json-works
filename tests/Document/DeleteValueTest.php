<?php

namespace JsonWorks\Tests\Document;

class DeleteValueTest extends \JsonWorks\Tests\Base
{
    public function testFromObject()
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

        $expected = '{
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
                        [false, {"lastName": "Smith"}]
                    ]
                }
        }';

        $document = $this->getDocument($schema, $data);

        $path = '/prop2/collection/3/1/firstName';
        $this->assertTrue($document->deleteValue($path), 'Testing success: '.$path);
        $this->assertEquals(json_decode($expected), $document->data);

        $path = '/prop2/collection/0/firstName';
        $this->assertFalse($document->deleteValue($path), 'Testing fail: '.$path);
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

        $expected = '[
                "item0",
                [
                    "item10",
                    [0, 1]
                ]
        ]';

        $document = $this->getDocument($schema, $data);

        $path = '/1/1/2';
        $this->assertTrue($document->deleteValue($path), 'Testing success: '.$path);
        $this->assertEquals(json_decode($expected), $document->data);

        $path = '/0/3';
        $this->assertFalse($document->deleteValue($path), 'Testing fail: '.$path);
    }
}
