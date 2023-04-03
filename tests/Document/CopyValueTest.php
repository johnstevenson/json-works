<?php declare(strict_types=1);

namespace JsonWorks\Tests\Document;

class CopyValueTest extends \JsonWorks\Tests\Base
{
    public function testSuccess(): void
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

        $expected = json_decode('{
            "prop1": {
                "collection": [
                    {"firstName": "Fred", "lastName": "Bloggs"},
                    {"firstName": "Harry", "lastName": "Smith"}
                ]
            },
            "prop2": {
                "collection": [
                    {"firstName": "Harry", "lastName": "Smith"}
                ]
             }
        }');

        $document = $this->getDocument($schema, $data);

        $fromPath = '/prop1/collection/1';
        $toPath = '/prop2/collection/-';

        self::assertTrue($document->copyValue($fromPath, $toPath));
        self::assertEquals($expected, $document->data);
    }

    public function testFail(): void
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
        self::assertFalse($document->copyValue($fromPath, $toPath));
    }
}
