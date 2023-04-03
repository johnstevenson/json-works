<?php declare(strict_types=1);
namespace JsonWorks\Tests\Helpers\Builder;

use JohnStevenson\JsonWorks\Helpers\Patch\Builder;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;

class BuilderTest extends \JsonWorks\Tests\Base
{
    protected Target $target;
    protected string $error;

    /**
     * @param mixed $data
     * @param string $path
     * @param mixed $value
     */
    protected function runBuilder(&$data, $path, &$value): bool
    {
        $this->error = '';
        $this->target = new Target($path, $this->error);
        $this->target->element =& $data;
        $builder = new Builder();

        try {
            $value = $builder->make($this->target, $value);
            $result = true;
        } catch (\InvalidArgumentException $e) {
            $result = false;
        }

        return $result;
    }

    public function testRootArray(): void
    {
        $data = null;

        $value = [1, 2, 3];
        $expected = $value;

        $path = '';

        $result = $this->runBuilder($data, $path, $value);

        self::assertTrue($result, 'Testing method returns true');
        self::assertEquals($expected, $value, 'Testing value is correct');
        self::assertEquals(Target::TYPE_VALUE, $this->target->type, 'Testing target type is TYPE_VALUE');
        self::assertFalse($this->sameRef($data, $value), 'Testing references are broken');
    }

    public function testRootObject(): void
    {
        $data = null;

        $value = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $expected = $value;

        $path = '';

        $result = $this->runBuilder($data, $path, $value);

        self::assertTrue($result, 'Testing method returns true');
        self::assertEquals($expected, $value, 'Testing value is correct');
        self::assertEquals(Target::TYPE_VALUE, $this->target->type, 'Testing target type is TYPE_VALUE');
        self::assertFalse($this->sameRef($data, $value), 'Testing references are broken');
    }

    public function testArraySingleLevel(): void
    {
        $data = json_decode('[
            0, 1
        ]');

        $value = ['inner'];

        $expected = $value;

        $path = '/1';

        $result = $this->runBuilder($data, $path, $value);

        // check success
        self::assertTrue($result, 'Testing method returns true');
        self::assertEquals($expected, $value, 'Testing value is correct');
        self::assertEquals(Target::TYPE_ARRAY, $this->target->type, 'Testing target type is TYPE_ARRAY');
        self::assertEquals(1, $this->target->key, 'Testing target key is 1');

        // check fail
        $path = '/4';

        $result = $this->runBuilder($data, $path, $value);
        self::assertFalse($result, 'Testing method returns false');
        self::assertStringContainsString('ERR_PATH_KEY', $this->error, 'Testing error is set');
    }

    public function testObjectSingleLevel(): void
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $value = "inner2 value";

        $expected = $value;

        $path = '/inner2';

        $result = $this->runBuilder($data, $path, $value);

        // check success
        self::assertTrue($result, 'Testing method returns true');
        self::assertEquals($expected, $value, 'Testing value is correct');
        self::assertEquals(Target::TYPE_OBJECT, $this->target->type, 'Testing target type is TYPE_OBJECT');
        self::assertEquals('inner2', $this->target->key, 'Testing target key is 1');
    }

    public function testArrayNestedLevel(): void
    {
        $res = json_encode(json_decode('{"": "value"}'));

        $json = json_encode(["" => "value"]);
        $data = json_decode('[
            0, 1
        ]');

        $value = 'inner';

        $expected = [ [$value] ];

        $path = '/2/-/-';

        $result = $this->runBuilder($data, $path, $value);

        // check success
        self::assertTrue($result, 'Testing method returns true');
        self::assertEquals($expected, $value, 'Testing value is correct');
        self::assertEquals(Target::TYPE_ARRAY, $this->target->type, 'Testing target type is TYPE_ARRAY');
        self::assertEquals(2, $this->target->key, 'Testing target key is 2');

        // check fail
        $path = '/item/-/-';

        $result = $this->runBuilder($data, $path, $value);
        self::assertFalse($result, 'Testing method returns false');
        self::assertStringContainsString('ERR_PATH_KEY', $this->error, 'Testing error is set');
    }

    public function testObjectMultiLevel(): void
    {
        $data = json_decode('{
            "prop1": {
                "inner1": [0, 1, 2, 3]
            }
        }');

        $value = "nested value";

        $expected = json_decode('{
            "inner3": {
                "inner4": {
                    "nested": "nested value"
                }
            }
        }');

        $path = '/inner2/inner3/inner4/nested';

        $result = $this->runBuilder($data, $path, $value);

        // check success
        self::assertTrue($result, 'Testing method returns true');
        self::assertEquals($expected, $value, 'Testing value is correct');
        self::assertEquals(Target::TYPE_OBJECT, $this->target->type, 'Testing target type is TYPE_OBJECT');
        self::assertEquals('inner2', $this->target->key, 'Testing target key is 1');
    }
}
