<?php declare(strict_types=1);

namespace JsonWorks\Tests\BaseDocument;

class ValidateTest extends \JsonWorks\Tests\Base
{
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

        self::assertTrue($this->validate($schema, $data));
    }
}
