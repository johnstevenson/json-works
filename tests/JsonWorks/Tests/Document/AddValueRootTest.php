<?php

namespace JsonWorks\Tests\Document;

class AddValueRootTest extends \JsonWorks\Tests\Base
{

    public function testObjectNoSchema()
    {
        $document = $this->getDocument(null, null);
        $path = '';
        $value = (object) array('prop1' => 1, 'prop2' => 'value');
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($value, $document->data);
    }

    public function testObjectWithSchema()
    {
        $schema = '{
            "type" : "object",
            "properties":
            {
                "prop1": {"type": "number"},
                "prop2": {"$ref": "#/definitions/alphanum"}
            },
            "definitions":
            {
                "alphanum": {
                    "oneOf": [ {"type": "string"}, {"type": "number"} ]
                }
            }
        }';

        $document = $this->getDocument($schema, null);
        $path = '';
        $value = (object) array('prop1' => 1, 'prop2' => 'value');
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($value, $document->data);
    }

    public function testObjectWithArrayPropertyNames()
    {
        $schema = '{
            "type" : "object",
            "patternProperties":
            {
                "^[0-9]*$": {"$ref": "#/definitions/alphanum"},
                "^-{1,1}$": {"$ref": "#/definitions/alphanum"}
            },
            "definitions":
            {
                "alphanum": {
                    "oneOf": [ {"type": "string"}, {"type": "number"} ]
                }
            }
        }';

        $document = $this->getDocument($schema, null);
        $path = '';
        $value = json_decode('{"0": 1, "-": "value"}');
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($value, $document->data);
    }

    public function testArrayNoSchema()
    {
        $document = $this->getDocument(null, null);
        $path = '';
        $value = array(1, 'value');
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($value, $document->data);
    }

    public function testArrayWithSchema()
    {
        $schema = '{
            "type" : "array",
            "items":
            {
                "oneOf": [ {"type": "string"}, {"type": "number"} ]
            }
        }';

        $document = $this->getDocument($schema, null);
        $path = '';
        $value = array(1, 'value');
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($value, $document->data);
    }

    public function testInvalid()
    {
        $document = $this->getDocument(null, null);
        $path = '';
        $value = 'value';
        $this->assertFalse($document->addValue($path, $value));
        $this->assertNull($document->data);

    }

    public function testSinglePushStrict()
    {
        $schema = '{
            "type" : "array",
            "items":
            {
                "type": "number"
            }
        }';

        $data = null;

        $document = $this->getDocument($schema, $data);
        $path = '/-';
        $value = 1;
        $expected = array($value);
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($expected, $document->data);
    }

    public function testMultiPushStrict()
    {
        $schema = '{
            "type" : "array",
            "items":
            {
                "oneOf": [ {"type": "array"}, {"type": "number"} ]
            }
        }';

        $data = null;

        $document = $this->getDocument($schema, $data);
        $path = '/-/-';
        $value = 1;
        $expected = array(array($value));
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($expected, $document->data);
     }
}
