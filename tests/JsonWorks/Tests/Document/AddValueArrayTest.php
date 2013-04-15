<?php

namespace JsonWorks\Tests\Document;

class AddValueArrayTest extends \JsonWorks\Tests\Base
{
    public function testSinglePushDashSchemaNone()
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

    public function testSinglePushDashSchemaArray()
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

    public function testSinglePushDashSchemaObject()
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

    public function testSinglePushZeroSchemaNone()
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

    public function testSinglePushZeroSchemaArray()
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

    public function testSinglePushZeroSchemaObject()
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

    public function testMultiPushDash()
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

    public function testMultiPushZero()
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

    public function testMultiPushMixed()
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
