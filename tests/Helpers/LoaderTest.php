<?php declare(strict_types=1);

namespace JsonWorks\Tests\Helpers;

use JohnStevenson\JsonWorks\Loader;

class LoaderTest extends \JsonWorks\Tests\Base
{
    const TYPE_DOCUMENT = 'LOAD_TYPE_DOCUMENT';
    const TYPE_SCHEMA = 'LOAD_TYPE_SCHEMA';
    const TYPE_PATCH = 'LOAD_TYPE_PATCH';

    protected Loader $loader;
    /** @var resource|null */
    protected $resource;

    protected function setUp(): void
    {
        $this->loader = new Loader();
    }

    protected function tearDown(): void
    {
        if ($this->resource !== null) {
            fclose($this->resource);
        }
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    protected function callLoader(string $loadType, $data)
    {
        switch ($loadType) {
            case self::TYPE_DOCUMENT:
                return $this->loader->getData($data);
            case self::TYPE_SCHEMA:
                return $this->loader->getSchema($data);
            case self::TYPE_PATCH:
                return $this->loader->getPatch($data);
            default:
                $msg = sprintf("Unknown load type '%s', test not run", $loadType);
                throw new \InvalidArgumentException($msg);
        }
    }

    /**
     * @return resource
     */
    protected function getResource()
    {
        if ($this->resource === null) {
            $file = __FILE__;

            if (false === ($fp = fopen($file, 'r'))) {
                throw new \RuntimeException('Test not run, unable to open: '.$file);
            }

            $this->resource = $fp;
        }

        return $this->resource;
    }

    /**
     * @return array<string>|array<string, mixed>
     */
    protected function getAllData(bool $keysOnly = false): array
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

    /**
     * @param array<mixed> $valid
     */
    protected function runInvalidTypesTest(string $loadType, array $valid): void
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
                self::fail('Exception not raised for '.$msg);
            } catch (\RuntimeException $e) {
                self::assertStringContainsString('ERR_BAD_INPUT', $e->getMessage(), $msg);
            }
        }
    }

    /**
     * @param array<mixed> $valid
     */
    protected function runValidTypesTest(string $loadType, array $valid): void
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
            self::assertTrue($result, $msg);
        }
    }

    protected function runInvalidFilesTest(string $loadType): void
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
                self::fail('Exception not raised for '.$msg);
            } catch (\RuntimeException $e) {
                self::assertStringContainsString($errMsg, $e->getMessage(), $msg);
            }
        }
    }

    protected function runValidFileTest(string $loadType, string $file): void
    {
        try {
            $filename = $this->getFixturePath($file);
            $this->callLoader($loadType, $filename);
            $result = true;
        } catch (\RuntimeException $e) {
            $result = false;
        }

        $msg = sprintf("%s test valid file '%s'", $loadType, $file);
        self::assertTrue($result, $msg);
    }

    public function testLoadDataTypes(): void
    {
        $loadType = self::TYPE_DOCUMENT;

        // everything is valid
        $valid = $this->getAllData(true);

        $this->runValidTypesTest($loadType, $valid);
        $this->runInvalidTypesTest($loadType, $valid);
    }

    public function testLoadDataFiles(): void
    {
        $loadType = self::TYPE_DOCUMENT;

        $this->runValidFileTest($loadType, 'pretty.json');
        $this->runInvalidFilesTest($loadType);
    }

    public function testLoadSchemaTypes(): void
    {
        $loadType = self::TYPE_SCHEMA;
        $valid = ['object'];

        $this->runValidTypesTest($loadType, $valid);
        $this->runInvalidTypesTest($loadType, $valid);
    }

    public function testLoadSchemaFiles(): void
    {
        $loadType = self::TYPE_SCHEMA;

        $this->runValidFileTest($loadType, 'schema.json');
        $this->runInvalidFilesTest($loadType);
    }

    public function testLoadPatchTypes(): void
    {
        $loadType = self::TYPE_PATCH;
        $valid = ['array'];

        $this->runValidTypesTest($loadType, $valid);
        $this->runInvalidTypesTest($loadType, $valid);
    }

    public function testLoadPatchFiles(): void
    {
        $loadType = self::TYPE_PATCH;

        $this->runValidFileTest($loadType, 'patch.json');
        $this->runInvalidFilesTest($loadType);
    }
}
