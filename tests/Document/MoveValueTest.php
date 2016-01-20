<?php

namespace JsonWorks\Tests\Document;

class MoveValueTest extends \JsonWorks\Tests\Base
{
    public function testSuccess()
    {
        $schema = null;
        $data = '{
            "prop1": {
                "collection": [
                    {"firstName": "Fred", "lastName": "Bloggs"},
                    {"firstName": "Harry", "lastName": "Smith"}
                ]
            }
        }';

        $expected = '{
            "prop1": {
                "collection": [
                    {"firstName": "Fred", "lastName": "Bloggs"}
                ]
            },
            "prop2": {
                "collection": [
                    {"firstName": "Harry", "lastName": "Smith"}
                ]
             }
        }';

        $document = $this->getDocument($schema, $data);

        $fromPath = '/prop1/collection/1';
        $toPath = '/prop2/collection/-';
        $this->assertTrue($document->moveValue($fromPath, $toPath));
        $this->assertEquals(json_decode($expected), $document->data);
    }

    public function testFailSchema()
    {
        $schema = null;
        $data = '{
            "prop1": {
                "collection": [
                    {"firstName": "Fred", "lastName": "Bloggs"},
                    {"firstName": "Harry", "lastName": "Smith"}
                ]
            }
        }';

        $document = $this->getDocument($schema, $data);

        $fromPath = '/prop1/collection/2';
        $toPath = '/prop2/collection/-';
        $this->assertFalse($document->moveValue($fromPath, $toPath));
    }
}
