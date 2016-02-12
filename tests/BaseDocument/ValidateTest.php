<?php

namespace JsonWorks\Tests\BaseDocument;

class ValidateTest extends \JsonWorks\Tests\Base
{
    public function testRainbowIssue()
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

        $this->assertTrue($this->validate($schema, $data));
    }
}
