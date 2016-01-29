<?php

namespace JsonWorks\Tests\Helpers;

use JohnStevenson\JsonWorks\Helpers\Loader;

class LoaderTest extends \JsonWorks\Tests\Base
{
    protected $loader;

    protected function setUp()
    {
        $this->loader = new Loader();
    }

    protected function runAllInvalidTypesTest($loadFunc)
    {
        $tests = [
            'true' => true,
            'false' => false,
            'integer' => 100,
            'double' => 3.142,
            'resource' => fopen(__FILE__, 'r'),
        ];

        foreach ($tests as $type => $data) {
            $this->runInvalidTypeTest($loadFunc, $type, $data);
        }

        fclose($tests['resource']);
    }

    protected function runInvalidTypeTest($loadFunc, $type, $data)
    {
        $msg = sprintf("%s test invalid '%s'", $loadFunc, $type);

        try {
            $this->loader->{$loadFunc}($data);
        } catch (\RuntimeException $e) {
            $this->assertContains('ERR_BAD_INPUT', $e->getMessage(), $msg);
            return;
        }

        $this->fail('Exception not raised for '.$msg);
    }

    protected function runValidTypeTest($loadFunc, $type, $data)
    {
        try {
            $this->loader->{$loadFunc}($data);
            $result = true;
        } catch (\RuntimeException $e) {
            $result = false;
        }

        $msg = sprintf("%s test valid '%s'", $loadFunc, $type);
        $this->assertTrue($result, $msg);
    }

    protected function runInvalidFileTest($loadFunc, $file)
    {
        $msg = sprintf("%s test invalid file '%s'", $loadFunc, $file);

        try {
            $filename = $this->getFixturePath($file);
            $this->loader->{$loadFunc}($filename);
        } catch (\RuntimeException $e) {
            $this->assertContains('ERR_BAD_INPUT', $e->getMessage(), $msg);
            return;
        }

        $this->fail('Exception not raised for '.$msg);
    }

    protected function runMissingFileTest($loadFunc)
    {
        $file = 'nofile.json';

        $msg = sprintf("%s test missing file '%s'", $loadFunc, $file);

        try {
            $filename = $this->getFixturePath($file);
            $this->loader->{$loadFunc}($filename);
        } catch (\RuntimeException $e) {
            $this->assertContains('ERR_NOT_FOUND', $e->getMessage(), $msg);
            return;
        }

        $this->fail('Exception not raised for '.$msg);
    }

    protected function runValidFileTest($loadFunc, $file)
    {
        try {
            $filename = $this->getFixturePath($file);
            $this->loader->{$loadFunc}($filename);
            $result = true;
        } catch (\RuntimeException $e) {
            $result = false;
        }

        $msg = sprintf("%s test valid file '%s'", $loadFunc, $file);
        $this->assertTrue($result, $msg);
    }

    public function testLoadDataTypes()
    {
        $this->runValidTypeTest('loadData', 'object', new \stdClass());
        $this->runValidTypeTest('loadData', 'array', []);
        $this->runValidTypeTest('loadData', 'null', null);

        $this->runAllInvalidTypesTest('loadData');
    }

    public function testLoadDataFiles()
    {
        $this->runValidFileTest('loadData', 'pretty.json');

        $this->runInvalidFileTest('loadData', 'invalid.json');
        $this->runInvalidFileTest('loadData', 'empty.json');
        $this->runMissingFileTest('loadData');
    }

    public function testLoadSchemaTypes()
    {
        $this->runValidTypeTest('loadSchema', 'object', new \stdClass());

        $this->runInvalidTypeTest('loadSchema', 'array', []);
        $this->runInvalidTypeTest('loadSchema', 'null', null);

        $this->runAllInvalidTypesTest('loadSchema');
    }

    public function testLoadSchemaFiles()
    {
        $this->runValidFileTest('loadSchema', 'schema.json');

        $this->runInvalidFileTest('loadSchema', 'invalid.json');
        $this->runInvalidFileTest('loadSchema', 'empty.json');
        $this->runMissingFileTest('loadSchema');
    }

    public function testLoadPatchTypes()
    {
        $this->runValidTypeTest('loadPatch', 'array', []);

        $this->runInvalidTypeTest('loadPatch', 'object', new \stdClass());
        $this->runInvalidTypeTest('loadPatch', 'null', null);

        $this->runAllInvalidTypesTest('loadPatch');
    }

    public function testLoadPatchFiles()
    {
        $this->runValidFileTest('loadPatch', 'patch.json');

        $this->runInvalidFileTest('loadPatch', 'invalid.json');
        $this->runInvalidFileTest('loadPatch', 'empty.json');
        $this->runMissingFileTest('loadPatch');
    }
}
