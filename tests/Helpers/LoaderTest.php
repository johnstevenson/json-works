<?php

namespace JsonWorks\Tests\Helpers;

use JohnStevenson\JsonWorks\Helpers\Loader;

class LoaderTest extends \JsonWorks\Tests\Base
{
    protected $loader;
    protected $resource;

    protected function setUp()
    {
        $this->loader = new Loader();
    }

    protected function tearDown()
    {
        if ($this->resource) {
            fclose($this->resource);
        }
    }

    protected function callLoader($loadType, $data)
    {
        switch ($loadType) {
            case Loader::TYPE_DOCUMENT:
                return $this->loader->loadData($data);
            case Loader::TYPE_SCHEMA:
                return $this->loader->loadSchema($data, $file);
            case Loader::TYPE_PATCH:
                return $this->loader->loadPatch($data);
            default:
                $msg = sprintf("Unknown load type '%s', test not run", $loadType);
                throw new \InvalidArgumentException($msg);
        }
    }

    protected function getResource()
    {
        if (!$this->resource) {
            $this->resource = fopen(__FILE__, 'r');
        }

        return $this->resource;
    }

    protected function getAllData($keysOnly = false)
    {
        $data = [
            'object'    => new \stdClass(),
            'array'     => [],
            'true'      => true,
            'false'     => false,
            'null'      => null,
            'string'    => 'hello',
            'integer'   => 100,
            'double'    => 3.142
        ];

        return $keysOnly ? array_keys($data) : $data;
    }

    protected function runInvalidTypesTest($loadType, $valid)
    {
        $tests = $this->getAllData();

        foreach ($valid as $key) {
            unset($tests[$key]);
        }

        $tests['resource'] = $this->getResource();

        foreach ($tests as $type => $data) {
            $msg = sprintf("%s test invalid '%s'", $loadType, $type);

            try {
                $this->callLoader($loadType, $data);
                $this->fail('Exception not raised for '.$msg);
            } catch (\RuntimeException $e) {
                $this->assertContains('ERR_BAD_INPUT', $e->getMessage(), $msg);
            }
        }
    }

    protected function runValidTypesTest($loadType, array $valid)
    {
        $allData = $this->getAllData();
        $tests = [];

        foreach ($valid as $key) {
            $tests[$key] = $allData[$key];
        }

        foreach ($tests as $type => $data) {

            try {
                $this->callLoader($loadType, $data);
                $result = true;
            } catch (\RuntimeException $e) {
                $result = false;
            }

            $msg = sprintf("%s test valid '%s'", $loadType, $type);
            $this->assertTrue($result, $msg);
        }
    }

    protected function runInvalidFilesTest($loadType)
    {
        $tests = [
            'invalid.json'  => 'ERR_BAD_INPUT',
            'empty.json'    => 'ERR_BAD_INPUT',
            'nofile.json'   => 'ERR_NOT_FOUND'
        ];

        foreach ($tests as $file => $errMsg) {
            $msg = sprintf("%s test invalid file '%s'", $loadType, $file);

            try {
                $filename = $this->getFixturePath($file);
                $this->callLoader($loadType, $filename);
                $this->fail('Exception not raised for '.$msg);
            } catch (\RuntimeException $e) {
                $this->assertContains($errMsg, $e->getMessage(), $msg);
            }
        }
    }

    protected function runValidFileTest($loadType, $file)
    {
        try {
            $filename = $this->getFixturePath($file);
            $this->callLoader($loadType, $filename);
            $result = true;
        } catch (\RuntimeException $e) {
            $result = false;
        }

        $msg = sprintf("%s test valid file '%s'", $loadType, $file);
        $this->assertTrue($result, $msg);
    }

    public function testLoadDataTypes()
    {
        $loadType = Loader::TYPE_DOCUMENT;

        // everything is valid
        $valid = $this->getAllData(true);

        $this->runValidTypesTest($loadType, $valid);
        $this->runInvalidTypesTest($loadType, $valid);
    }

    public function testLoadDataFiles()
    {
        $loadType = Loader::TYPE_DOCUMENT;

        $this->runValidFileTest($loadType, 'pretty.json');
        $this->runInvalidFilesTest($loadType);
    }

    public function testLoadSchemaTypes()
    {
        $loadType = Loader::TYPE_SCHEMA;
        $valid = ['object'];

        $this->runValidTypesTest($loadType, $valid);
        $this->runInvalidTypesTest($loadType, $valid);
    }

    public function testLoadSchemaFiles()
    {
        $loadType = Loader::TYPE_SCHEMA;

        $this->runValidFileTest($loadType, 'schema.json');
        $this->runInvalidFilesTest($loadType);
    }

    public function testLoadPatchTypes()
    {
        $loadType = Loader::TYPE_PATCH;
        $valid = ['array'];

        $this->runValidTypesTest($loadType, $valid);
        $this->runInvalidTypesTest($loadType, $valid);
    }

    public function testLoadPatchFiles()
    {
        $loadType = Loader::TYPE_PATCH;

        $this->runValidFileTest($loadType, 'patch.json');
        $this->runInvalidFilesTest($loadType);
    }
}
