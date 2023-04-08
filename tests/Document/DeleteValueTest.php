<?php declare(strict_types=1);

namespace JsonWorks\Tests\Document;

class DeleteValueTest extends \JsonWorks\Tests\Base
{
    public function testFromObject(): void
    {
        $schema = null;
        $data = '{
            "prop1": {
                "firstName": "Fred"
            },
            "prop2": {
                "collection": [
                    "item0",
                    {"firstName": "Fred", "lastName": "Bloggs"},
                    "item2",
                    [false, {"firstName": "Harry", "lastName": "Smith"}]
                ]
            }
        }';

        $expected = '{
            "prop1": {
                "firstName": "Fred"
            },
            "prop2": {
                "collection": [
                    "item0",
                    {"firstName": "Fred", "lastName": "Bloggs"},
                    "item2",
                    [false, {"lastName": "Smith"}]
                ]
            }
        }';

        $document = $this->getDocument($schema, $data);

        $path = '/prop2/collection/3/1/firstName';
        self::assertTrue($document->deleteValue($path), 'Testing success: '.$path);
        self::assertEquals(json_decode($expected), $document->getData());

        $path = '/prop2/collection/0/firstName';
        self::assertFalse($document->deleteValue($path), 'Testing fail: '.$path);
    }

    public function testFromArray(): void
    {
        $schema = null;
        $data = '[
            "item0", [
                "item10",
                [0, 1, "item112 value"]
            ]
        ]';

        $expected = '[
            "item0", [
                "item10",
                [0, 1]
            ]
        ]';

        $document = $this->getDocument($schema, $data);

        $path = '/1/1/2';
        self::assertTrue($document->deleteValue($path), 'Testing success: '.$path);
        self::assertEquals(json_decode($expected), $document->getData());

        $path = '/0/3';
        self::assertFalse($document->deleteValue($path), 'Testing fail: '.$path);
    }

    public function testObjectPropertyFromRoot(): void
    {
        $schema = null;
        $data = '{
            "prop1": {
                "firstName": "Fred"
            }
        }';

        $expected = '{}';

        $document = $this->getDocument($schema, $data);

        $path = '/prop1';
        self::assertTrue($document->deleteValue($path));
        self::assertEquals(json_decode($expected), $document->getData());
    }

    public function testArrayItemFromRoot(): void
    {
        $schema = null;
        $data = '["item0"]';

        $expected = '[]';

        $document = $this->getDocument($schema, $data);

        $path = '/0';
        self::assertTrue($document->deleteValue($path));
        self::assertEquals(json_decode($expected), $document->getData());
    }

    public function testObjectFromRoot(): void
    {
        $schema = null;
        $data = '{
            "prop1": {
                "firstName": "Fred"
            }
        }';

        $expected = null;

        $document = $this->getDocument($schema, $data);

        $path = '';
        self::assertTrue($document->deleteValue($path));
        self::assertEquals($expected, $document->getData());
    }

    public function testArrayFromRoot(): void
    {
        $schema = null;
        $data = '["item0"]';

        $expected = null;

        $document = $this->getDocument($schema, $data);

        $path = '';
        self::assertTrue($document->deleteValue($path));
        self::assertEquals($expected, $document->getData());
    }
}
