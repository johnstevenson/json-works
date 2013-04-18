<?php

namespace JsonWorks\Tests\Document;

class AddValueArrayPushTest extends \JsonWorks\Tests\Base
{
    public function testRootSingleDashSchemaNone()
    {
        $schema = null;
        $data = null;

        $document = $this->getDocument($schema, $data);
        $path = '/-';
        $value = 1;
        $expected = array($value);
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($expected, $document->data);
    }

    public function testRootSingleDashSchemaArray()
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

    public function testRootSingleDashSchemaObject()
    {
        $schema = '{
            "type" : "object",
            "properties":
            {
                "type": "number"
            }
        }';

        $data = null;

        $document = $this->getDocument($schema, $data);
        $path = '/-';
        $value = 1;
        $expected = (object) array('-' => $value);
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($expected, $document->data);
    }

    public function testRootSingleZeroSchemaNone()
    {
        $schema = null;
        $data = null;

        $document = $this->getDocument($schema, $data);
        $path = '/0';
        $value = 1;
        $expected = array($value);
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($expected, $document->data);
    }

    public function testRootSingleZeroSchemaArray()
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
        $path = '/0';
        $value = 1;
        $expected = array($value);
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($expected, $document->data);
    }

    public function testRootSingleZeroSchemaObject()
    {
        $schema = '{
            "type" : "object",
            "properties":
            {
                "type": "number"
            }
        }';

        $data = null;

        $document = $this->getDocument($schema, $data);
        $path = '/0';
        $value = 1;
        $expected = (object) array('0' => $value);
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($expected, $document->data);
    }

    public function testRootMultiDash()
    {
        $schema = '{
            "type": "array",
            "items":
            {
                "type": "array",
                "items": {
                    "type": "array",
                    "items": {
                        "type": "number"
                    }
                }
            }
        }';

        $data = null;

        $document = $this->getDocument($schema, $data);
        $path = '/-/-/-';
        $value = 1;
        $expected = array(array(array($value)));
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($expected, $document->data);
    }

    public function testRootMultiZero()
    {
        $schema = '{
            "type": "array",
            "items":
            {
                "type": "array",
                "items": {
                    "type": "array",
                    "items": {
                        "type": "number"
                    }
                }
            }
        }';

        $data = null;

        $document = $this->getDocument($schema, $data);
        $path = '/0/0/0';
        $value = 1;
        $expected = array(array(array($value)));
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($expected, $document->data);
    }

    public function testRootMultiMixed()
    {
        $schema = '{
            "type": "array",
            "items":
            {
                "type": "array",
                "items": {
                    "type": "array",
                    "items": {
                        "type": "number"
                    }
                }
            }
        }';

        $data = null;

        $document = $this->getDocument($schema, $data);
        $path = '/-/0/-';
        $value = 1;
        $expected = array(array(array($value)));
        $this->assertTrue($document->addValue($path, $value));
        $this->assertEquals($expected, $document->data);
    }
}
