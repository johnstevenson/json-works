<?php declare(strict_types=1);

namespace JsonWorks\Tests\BaseDocument;

use JohnStevenson\JsonWorks\Document;

/**
 * These tests call the validator from the document
 */
class ValidateTest extends \JsonWorks\Tests\Base
{
    public function testInvalidSchema(): void
    {
        $schema = '{
           "type": "object",
           "properties": {
               "prop1": { "$ref": "#/$defs/no-def" }
           }
        }';

        $data = '{
            "prop1" : [0, 1]
        }';

        $document = new Document();
        $document->loadData($data);
        $document->loadSchema($schema);

        self::assertFalse($document->validate());
        self::assertStringContainsString('Unable to find $ref', $document->getError());
    }

    public function testMultipleUsage(): void
    {
        $document = new Document();

        $schema = '{
            "items": {
                "type": "integer"
            },
            "additionalItems": false
        }';

        $data = [1, 2];

        $document->loadData($data);
        $document->loadSchema($schema);
        self::assertTrue($this->validate($schema, $data));

        $schema = '{
            "items": {
                "type": "string"
            },
            "additionalItems": false
        }';

        $data = ['1', '2'];

        $document->loadData($data);
        $document->loadSchema($schema);
        self::assertTrue($this->validate($schema, $data));
    }

    public function testRainbowIssue(): void
    {
        $schema = '{
           "type": "object",
           "properties": {
               "root" : {
                   "type" : ["array"],
                   "oneOf": [
                        {"$ref": "#/definitions/treeObj"}
                   ]
                }
           },
           "required" : [
               "root"
           ],
           "definitions": {
               "treeObj": {
                   "type": "array",
                   "items" : {
                       "oneOf": [
                            { "type" : "string"},
                            { "$ref": "#/definitions/treeObj" }
                       ]
                   }
               }
           }
        }';

        $data = '{
            "root" : [
                "tree-heading1",
                [
                    "tree-heading2",
                    [
                        "tree-heading3",
                        [
                            "deepest-heading"
                        ]
                    ]
                ]
            ]
        }';

        $document = new Document();
        $document->loadData($data);
        $document->loadSchema($schema);

        $result = $document->validate();

        self::assertTrue($result);
    }
}
