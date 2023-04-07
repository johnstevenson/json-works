<?php declare(strict_types=1);

namespace JsonWorks\Tests\Helpers;

use \stdClass;

use JohnStevenson\JsonWorks\Helpers\Formatter;

class FormatCopyTest extends \JsonWorks\Tests\Base
{
    protected Formatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new Formatter();
    }

    public function testFromObject(): void
    {
        $obj1 = (object) ['firstname' => 'Fred', 'lastName' => 'Bloggs'];
        $obj2 = (object) $this->formatter->copy($obj1);

        $obj1->lastName = 'Smith';
        $expected = 'Bloggs';

        self::assertEquals($expected, $obj2->lastName);
    }

    public function testFromAssoc(): void
    {
        $arr = ['firstname' => 'Fred', 'lastName' => 'Bloggs'];
        $obj = $this->formatter->copy($arr);

        $expected = (object) $arr;
        self::assertEquals($expected, $obj);
    }

    public function testDeepFromObject(): void
    {
        $obj = (object) ['firstname' => 'Fred', 'lastName' => 'Bloggs'];
        $obj1 = (object) ['users' => [$obj]];
        $obj2 = (object) $this->formatter->copy($obj1);

        $obj1->users[0]->lastName = 'Smith';
        $expected = 'Bloggs';
        self::assertEquals($expected, $obj2->users[0]->lastName);
    }

    public function testDeepFromAssoc(): void
    {
        $arr = ['firstname' => 'Fred', 'lastName' => 'Bloggs'];
        $obj1 = (object) ['users' => [$arr]];
        $obj2 = (object) $this->formatter->copy($obj1);

        $expected = (object) $arr;
        self::assertEquals($expected, $obj2->users[0]);
    }

    public function testArrayDeepFromObject(): void
    {
        $obj = (object) ['firstname' => 'Fred', 'lastName' => 'Bloggs'];
        $obj1 = (object) ['users' => [$obj]];
        $arr1 = [9, $obj1];

        /** @var array{0: int, 1: stdClass } */
        $arr2 = (array) $this->formatter->copy($arr1);

        $arr1[1]->users[0]->lastName = 'Smith';
        $expected = 'Bloggs';
        self::assertEquals($expected, $arr2[1]->users[0]->lastName);
    }

    public function testArrayDeepFromAssoc(): void
    {
        $arr = ['firstname' => 'Fred', 'lastName' => 'Bloggs'];
        $obj = (object) ['users' => [$arr]];
        $arr1 = [9, $obj];

        /** @var array{0: int, 1: stdClass } */
        $arr2 = (array) $this->formatter->copy($arr1);

        $expected = (object) $arr;
        self::assertEquals($expected, $arr2[1]->users[0]);
    }

    public function testObjectFromEmptyObject(): void
    {
        $obj = new \stdClass();
        $result = $this->formatter->copy($obj);

        $expected = new \stdClass();
        self::assertEquals($expected, $result);
    }

    public function testArrayFromEmptyArray(): void
    {
        $arr = [];
        $result = $this->formatter->copy($arr);
        $expected = [];
        self::assertEquals($expected, $result);
    }

    public function testArrayMixedToObject(): void
    {
        $arr = ['Bloggs', 'firstName' => 'Fred', 9];
        $result = $this->formatter->copy($arr);

        $expected = '{
            "0": "Bloggs",
            "firstName": "Fred",
            "1": 9
        }';

        $expected = $this->objectFromJson($expected);
        self::assertEquals($expected, $result);
    }

    public function testObjectWithEmptyElements(): void
    {
        $data = new \stdClass();
        $data->prop1 = new \stdClass();
        $data->prop2 = 'none';
        $data->prop3 = null;
        $data->prop4 = [];
        $data->prop5 = [7];
        $data->prop6 = ['Bloggs', 'firstName' => 'Fred', 9];
        $result = $this->formatter->copy($data);

        $expected = '{
            "prop1": {},
            "prop2": "none",
            "prop3": null,
            "prop4": [],
            "prop5": [7],
            "prop6": {
                "0": "Bloggs",
                "firstName": "Fred",
                "1": 9
            }
        }';

        $expected = $this->objectFromJson($expected);
        self::assertEquals($expected, $result);
    }
}
