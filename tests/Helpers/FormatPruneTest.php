<?php declare(strict_types=1);

namespace JsonWorks\Tests\Helpers;

use JohnStevenson\JsonWorks\Helpers\Formatter;

class FormatPruneTest extends \JsonWorks\Tests\Base
{
    protected Formatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new Formatter();
    }

    public function testNoData(): void
    {
        $data = '';
        $expected = '';

        $result = $this->formatter->prune(json_decode($data));
        self::assertEquals(json_decode($expected), $result);
    }

    public function testSimple(): void
    {
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

        $data = $this->objectFromJson($data);
        $expected = $this->objectFromJson($expected);

        $result = $this->formatter->prune($data);
        self::assertEquals($expected, $result);
    }

    public function testNested(): void
    {
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

        $data = $this->objectFromJson($data);
        $expected = $this->objectFromJson($expected);

        $result = $this->formatter->prune($data);
        self::assertEquals($expected, $result);
    }
}
