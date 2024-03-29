<?php declare(strict_types=1);

namespace JsonWorks\Tests\BaseDocument;

use JohnStevenson\JsonWorks\Utils;

class ToJsonTest extends \JsonWorks\Tests\Base
{
    public function testNoData(): void
    {
        $schema = null;
        $data = null;
        $expected = null;

        $document = $this->getDocument($schema, $data);
        $document->tidy();

        $json = $document->toJson(false);
        self::assertTrue($document->validate());
        self::assertEquals($this->getExpectedJson($expected), $json);
    }

    public function testInvalidJson(): void
    {
        $data = (object) ['prop1' => "\xc3\x28"];

        $document = new \JohnStevenson\JsonWorks\Document();
        $document->loadData($data);

        $json = $document->toJson(true);
        self::assertNull($json);
        self::assertStringContainsString('UTF-8', $document->getError());
    }

    public function testPathWithSlash(): void
    {
        $schema = null;

        $data = '{
            "prop1": "path/to/somewhere"
        }';

        $expected = '{"prop1":"path/to/somewhere"}';

        $document = $this->getDocument($schema, $data);

        $json = $document->toJson(false);
        self::assertEquals($expected, $json);
    }

    public function testEscapeUnicode(): void
    {
        $schema = null;

        $data = '{
            "prop1": "\\u018c"
        }';

        $expected = '{"prop1":"ƌ"}';

        $document = $this->getDocument($schema, $data);

        $json = $document->toJson(false);
        self::assertEquals($expected, $json);
    }

    public function testTidyNoSchema(): void
    {
        $schema = null;

        $data = '{
            "prop1": "",
            "prop2": {},
            "prop3": [],
            "prop4": null
        }';

        $expected = '{
            "prop1": "",
            "prop4": null
        }';

        // Do not use getDocument, so we have a null schema for test coverage
        $document = new \JohnStevenson\JsonWorks\Document();
        $document->loadData($data);


        $document->tidy();

        $json = $document->toJson(false);
        self::assertTrue($document->validate());
        self::assertEquals($this->getExpectedJson($expected), $json);
    }

    public function testNoTidySchema(): void
    {
        $schema = '{
            "required": ["prop2"]
        }';

        $data = '{
            "prop1": "",
            "prop2": {},
            "prop3": [],
            "prop4": null
        }';

        $expected = $data;

        $document = $this->getDocument($schema, $data);

        $json = $document->toJson(false);
        self::assertTrue($document->validate());
        self::assertEquals($this->getExpectedJson($expected), $json);
    }

    public function testTidySchemaFail(): void
    {
        $schema = '{
            "required": ["prop2"]
        }';

        $data = '{
            "prop1": "",
            "prop2": {},
            "prop3": [],
            "prop4": null
        }';

        $document = $this->getDocument($schema, $data);
        // Set order to true for test coverage
        $document->tidy(true);

        self::assertFalse($document->validate());
    }

    public function testTidyNestedNoSchema(): void
    {
        $schema = null;

        $data = '{
            "prop1": {
                "nested1": {
                    "nested2": {
                        "inner1": ["hidden"],
                        "inner2": {}
                    },
                    "nested3": []
                }
            },
            "prop2": "string",
            "prop3": [],
            "prop4": null
        }';

        $expected = '{
            "prop1": {
                "nested1": {
                    "nested2": {
                        "inner1": ["hidden"]
                    }
                }
            },
            "prop2": "string",
            "prop4": null
        }';

        $document = $this->getDocument($schema, $data);
        $document->tidy();

        $json = $document->toJson(false);
        self::assertTrue($document->validate());
        self::assertEquals($this->getExpectedJson($expected), $json);
    }

    public function testNoTidyNestedSchema(): void
    {
        $schema = '{
            "properties": {
                "prop1": {
                    "properties": {
                        "nested1": {
                            "required": ["nested3"]
                        }
                    }
                }
            }
        }';

        $data = '{
            "prop1": {
                "nested1": {
                    "nested2": {
                        "inner1": ["hidden"],
                        "inner2": {}
                    },
                    "nested3": []
                }
            },
            "prop2": "string",
            "prop3": [],
            "prop4": null
        }';

        $expected = $data;

        $document = $this->getDocument($schema, $data);

        $json = $document->toJson(false);
        self::assertTrue($document->validate());
        self::assertEquals($this->getExpectedJson($expected), $json);
    }

    public function testTidyNestedSchemaFail(): void
    {
        $schema = '{
            "properties": {
                "prop1": {
                    "properties": {
                        "nested1": {
                            "required": ["nested3"]
                        }
                    }
                }
            }
        }';

        $data = '{
            "prop1": {
                "nested1": {
                    "nested2": {
                        "inner1": ["hidden"],
                        "inner2": {}
                    },
                    "nested3": []
                }
            },
            "prop2": "string",
            "prop3": [],
            "prop4": null
        }';

        $document = $this->getDocument($schema, $data);
        $document->tidy();
        self::assertFalse($document->validate());
    }

    public function testPretty(): void
    {
        $schema = null;

        $data = '{
            "prop1": "",
            "prop2": {
                "inner1": [
                    {"lat": 50, "lng": 120},
                    {"lat": 27, "lng": 3}
                ],
                "inner2": 2
            },
            "prop3": [],
            "prop4": null,
            "prop5": 5,
            "prop6": {}
        }';

        $expected = $this->getFixtureFile('pretty.json');
        $document = $this->getDocument($schema, $data);

        $json = $document->toJson(true);
        self::assertEquals($expected, $json);
    }
}
