<?php declare(strict_types=1);

namespace JsonWorks\Tests\Document;

class MoveValueTest extends \JsonWorks\Tests\Base
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
                    {"firstName": "Fred", "lastName": "Bloggs"}
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
        self::assertTrue($document->moveValue($fromPath, $toPath));
        self::assertEquals($expected, $document->getData());
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
        self::assertFalse($document->moveValue($fromPath, $toPath));
    }
}
