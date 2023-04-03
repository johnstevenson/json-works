<?php declare(strict_types=1);

namespace JsonWorks\Tests\Document;

class GetValueTest extends \JsonWorks\Tests\Base
{
    public function testFromObject(): void
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

        $result = $document->getValue($path);
        self::assertEquals($expected, $result, 'Testing success: '.$path);

        $path = '/prop1/prop111';
        $result = $document->getValue($path);
        self::assertNull($result, 'Testing fail: '.$path);
    }

    public function testFromArray(): void
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
        self::assertTrue($document->hasValue($path, $value), 'Testing success: '.$path);
        self::assertEquals($expected, $value);

        $path = '/0/3';
        self::assertFalse($document->hasValue($path, $value), 'Testing fail: '.$path);
        self::assertNull($value);
    }

    public function testFromMixed(): void
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

        // success tests
        $path = '/prop1/firstName';
        $expected = 'Fred';
        self::assertTrue($document->hasValue($path, $value), 'Testing success: '.$path);
        self::assertEquals($expected, $value);

        $path = '/prop2/collection';
        self::assertTrue($document->hasValue($path, $value), 'Testing success: '.$path);
        self::assertIsArray($value);

        $path = '/prop2/collection/1/lastName';
        $expected = 'Bloggs';
        self::assertTrue($document->hasValue($path, $value), 'Testing success: '.$path);
        self::assertEquals($expected, $value);

        $path = '/prop2/collection/3/1/firstName';
        $expected = 'Harry';
        self::assertTrue($document->hasValue($path, $value), 'Testing success: '.$path);
        self::assertEquals($expected, $value);

        // fail tests
        $path = '/prop1/lastName';
        self::assertFalse($document->hasValue($path, $value), 'Testing fail: '.$path);
        self::assertNull($value);

        $path = '/prop2/collection/0/lastName';
        self::assertFalse($document->hasValue($path, $value), 'Testing fail: '.$path);
        self::assertNull($value);

        $path = '/prop2/collection/3/0/firstName';
        self::assertFalse($document->hasValue($path, $value), 'Testing fail: '.$path);
        self::assertNull($value);
    }
}
