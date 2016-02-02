<?php

namespace JsonWorks\Tests\BaseDocument;

use JohnStevenson\JsonWorks\BaseDocument;

class ValidateTest extends \JsonWorks\Tests\Base
{
    public function testRainbowIssue()
    {
        $schema = '{
           "$schema": "http://json-schema.org/draft-04/schema#",
           "title": "Experiment",
           "description": "Testing recursive objects",
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
                "some-tree-heading",
                [
                    "some-tree-deeper-heading",
                    [
                        "some-even-deeper-heading",
                        [
                            "the-deepest-heading"
                        ]
                    ]
                ]
            ]
        }';

        //$this->assertTrue($this->validate($schema, $data));
        $this->markTestIncomplete('Needs more work');
    }
}
