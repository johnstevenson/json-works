<?php

namespace JsonWorks\Tests\Document;

class CopyValueTest extends \JsonWorks\Tests\Base
{
    public function testSuccess()
    {
        $schema = null;
        $data = '{
                "prop1":
                {
                    "collection":
                    [
                        {"firstName": "Fred", "lastName": "Bloggs"},
                        {"firstName": "Harry", "lastName": "Smith"}
                    ]
                }
        }';

        $expected = '{
                "prop1":
                {
                    "collection":
                    [
                        {"firstName": "Fred", "lastName": "Bloggs"},
                        {"firstName": "Harry", "lastName": "Smith"}
                    ]
                },
                "prop2":
                {
                    "collection":
                    [
                        {"firstName": "Harry", "lastName": "Smith"}
                    ]
                 }
        }';

        $document = $this->getDocument($schema, $data);

        $fromPath = '/prop1/collection/1';
        $toPath = '/prop2/collection/-';
        $this->assertTrue($document->copyValue($fromPath, $toPath), 'Testing success');
        $this->assertEquals(json_decode($expected), $document->data);

        $fromPath = '/prop1/collection/2';
        $this->assertFalse($document->copyValue($fromPath, $toPath), 'Testing fail');
    }

    public function testFailSchema()
    {
        $schema = '{
            "properties": {
                "prop1": {}
            },
            "additionalProperties": false
        }';

        $data = '{
                "prop1":
                {
                    "collection":
                    [
                        {"firstName": "Fred", "lastName": "Bloggs"},
                        {"firstName": "Harry", "lastName": "Smith"}
                    ]
                }
        }';

        $document = $this->getDocument($schema, $data);

        $fromPath = '/prop1/collection/1';
        $toPath = '/prop2/collection/-';
        $this->assertFalse($document->copyValue($fromPath, $toPath), 'Testing fail');
    }

}
