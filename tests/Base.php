<?php declare(strict_types=1);

namespace JsonWorks\Tests;

use \stdClass;

use JohnStevenson\JsonWorks\Document;
use JohnStevenson\JsonWorks\Helpers\Utils;
use JohnStevenson\JsonWorks\Schema\Validator;

class Base extends \PHPUnit\Framework\TestCase
{
    /**
     * @param mixed $data
     */
    protected function validate(?string $schema, $data): bool
    {
        $schema = $this->getSchemaObject($schema);
        $data = $this->getValidData($data);

        $validator = new Validator($schema);
        return $validator->check($data);
    }

    /**
     * @param mixed $data
     * @return array{0: bool, 1: string}
     */
    protected function validateEx(?string $schema, $data, string $message = ''): array
    {
        $schema = $this->getSchemaObject($schema);
        $data = $this->getValidData($data);

        $validator = new Validator($schema);

        if (!$result = $validator->check($data)) {
            $error = $validator->getLastError();
            if (Utils::stringNotEmpty($message)) {
                $message = sprintf('%s:%s', $message, $error);
            }
        }

        return [$result, $message];
    }

    protected function getDocument(?string $schema, ?string $data): Document
    {
        $schema = $this->getSchemaObject($schema);
        $data = $data ?? null;

        if ($data !== null) {
            $data = $this->decodeJson($data);
        }

        $document = new Document();
        $document->loadData($data);
        $document->loadSchema($schema);
        return $document;
    }

    protected function getSchema(?string $schema): stdClass
    {
        return $this->getSchemaObject($schema);
    }

    /**
     * @param class-string $className
     * @param array<mixed> $args
     * @return mixed
     */
    protected function callMethod(string $className, string $methodName, array $args = [])
    {
        $reflection = new \ReflectionClass($className);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        $class = $reflection->newInstance();
        return $method->invokeArgs($class, $args);
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    protected function getValidData($data)
    {
        if (is_string($data)) {
            $data = trim($data);

            if (Utils::stringIsJson($data)) {
                $data = $this->decodeJson($data);
            }
        }

        return $data;
    }

    protected function getSchemaObject(?string $schema): stdClass
    {
        $schema = $schema ?? '{}';

        return $this->objectFromJson($schema);
    }

    /**
     * @return mixed
     */
    protected function decodeJson(string $json)
    {
        $result = json_decode($json);

        if (null === $result) {
            throw new \InvalidArgumentException('Test not run, json not valid');
        }

        return $result;
    }

    protected function objectFromJson(string $json): stdClass
    {
        $result = json_decode($json);

        if (!($result instanceof stdClass)) {
            throw new \InvalidArgumentException('Test not run, json is not an object');
        }

        return $result;
    }

    /**
     * @return array<mixed>
     */
    protected function arrayFromJson(string $json): array
    {
        $result = $this->decodeJson($json);

        if (!is_array($result)) {
            throw new \InvalidArgumentException('Test not run, json is not an array');
        }

        return $result;
    }

    /**
     * Formats json string to a single line
     */
    protected function getExpectedJson(?string $expected): string
    {
        $expected = $expected ?? '';
        $result = json_encode(json_decode($expected));

        if ($result === false) {
            throw new \InvalidArgumentException('Test not run, expected json is invalid');
        }

        return $result;
    }

    protected function getFixtureFile(string $filename): string
    {
        $contents = file_get_contents($this->getFixturePath($filename));

        if ($contents === false) {
            throw new \InvalidArgumentException('Test not run, fixture file cannot be opened');
        }

        return $contents;
    }

    protected function getFixturePath(string $filename): string
    {
        return __DIR__.'/Fixtures/'.$filename;
    }

    /**
     * @param mixed $var1
     * @param mixed $var2
     */
    protected function sameRef(&$var1, &$var2): bool
    {
        if ($var1 !== $var2) {
            return false;
        }
        // backup $var1
        $tmp = $var1;

        $var1 = ($var1 === true) ? false : true;
        $result = $var1 === $var2;

        // restore $var1
        $var1 = $tmp;

        return $result;
    }
}
