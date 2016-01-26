<?php
namespace JsonWorks\Tests\Helpers\Builder;

use JohnStevenson\JsonWorks\Helpers\Patch\Builder;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;

class BuilderTest extends \JsonWorks\Tests\Base
{
    protected $target;
    protected $error;

    protected function runBuilder(&$data, $path, &$value)
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

    public function testRootArray()
    {
        $data = null;

        $value = [1, 2, 3];
        $expected = $value;

        $path = '';

        $result = $this->runBuilder($data, $path, $value);

        $this->assertTrue($result, 'Testing method returns true');
        $this->assertEquals($expected, $value, 'Testing value is correct');
        $this->assertEquals(Target::TYPE_VALUE, $this->target->type, 'Testing target type is TYPE_VALUE');
        $this->assertFalse($this->sameRef($data, $value, 'Testing references are broken'));
    }

    public function testRootObject()
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

        $this->assertTrue($result, 'Testing method returns true');
        $this->assertEquals($expected, $value, 'Testing value is correct');
        $this->assertEquals(Target::TYPE_VALUE, $this->target->type, 'Testing target type is TYPE_VALUE');
        $this->assertFalse($this->sameRef($data, $value), 'Testing references are broken');
    }

    public function testArraySingleLevel()
    {
        $data = json_decode('[
            0, 1
        ]');

        $value = ['inner'];

        $expected = $value;

        $path = '/1';

        $result = $this->runBuilder($data, $path, $value);

        // check success
        $this->assertTrue($result, 'Testing method returns true');
        $this->assertEquals($expected, $value, 'Testing value is correct');
        $this->assertEquals(Target::TYPE_ARRAY, $this->target->type, 'Testing target type is TYPE_ARRAY');
        $this->assertEquals(1, $this->target->key, 'Testing target key is 1');

        // check fail
        $path = '/4';

        $result = $this->runBuilder($data, $path, $value);
        $this->assertFalse($result, 'Testing method returns false');
        $this->assertContains('ERR_PATH_KEY', $this->error, 'Testing error is set');
    }

    public function testObjectSingleLevel()
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
        $this->assertTrue($result, 'Testing method returns true');
        $this->assertEquals($expected, $value, 'Testing value is correct');
        $this->assertEquals(Target::TYPE_OBJECT, $this->target->type, 'Testing target type is TYPE_OBJECT');
        $this->assertEquals('inner2', $this->target->key, 'Testing target key is 1');
    }

    public function testArrayNestedLevel()
    {
        $data = json_decode('[
            0, 1
        ]');

        $value = 'inner';

        $expected = [ [$value] ];

        $path = '/2/-/-';

        $result = $this->runBuilder($data, $path, $value);

        // check success
        $this->assertTrue($result, 'Testing method returns true');
        $this->assertEquals($expected, $value, 'Testing value is correct');
        $this->assertEquals(Target::TYPE_ARRAY, $this->target->type, 'Testing target type is TYPE_ARRAY');
        $this->assertEquals(2, $this->target->key, 'Testing target key is 2');

        // check fail
        $path = '/item/-/-';

        $result = $this->runBuilder($data, $path, $value);
        $this->assertFalse($result, 'Testing method returns false');
        $this->assertContains('ERR_PATH_KEY', $this->error, 'Testing error is set');
    }

    public function testObjectMultiLevel()
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
        $this->assertTrue($result, 'Testing method returns true');
        $this->assertEquals($expected, $value, 'Testing value is correct');
        $this->assertEquals(Target::TYPE_OBJECT, $this->target->type, 'Testing target type is TYPE_OBJECT');
        $this->assertEquals('inner2', $this->target->key, 'Testing target key is 1');
    }
}
