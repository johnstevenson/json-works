<?php declare(strict_types=1);

namespace JsonWorks\Tests;

use JohnStevenson\JsonWorks\Document;
use JohnStevenson\JsonWorks\Helpers\Utils;
use JohnStevenson\JsonWorks\Schema\Validator;

class Base extends \PHPUnit\Framework\TestCase
{
    /**
     * @param mixed $schema
     * @param mixed $data
     */
    protected function validate($schema, $data): bool
    {
        $schema = $this->getSchemaObject($schema);
        $data = $this->getValidData($data);

        $validator = new Validator($schema);
        return $validator->check($data);
    }

    /**
     * @param mixed $schema
     * @param mixed $data
     * @return array{0: bool, 1: string}
     */
    protected function validateEx($schema, $data, string $message = ''): array
    {
        $schema = $this->getSchemaObject($schema);
        $data = $this->getValidData($data);

        $validator = new Validator($schema);

        if (!$result = $validator->check($data)) {
            $error = $validator->getErrors(true);
            if (Utils::stringNotEmpty($message)) {
                $message = sprintf('%s:%s', $message, $error);
            }
        }

        return [$result, $message];
    }

    /**
     * @param mixed $schema
     * @param mixed $data
     */
    protected function getDocument($schema, $data): Document
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

    protected function getSchema(?string $schema): object
    {
        return $this->getSchemaObject($schema);
    }

    /**
     * @param string|object $obj
     * @param string $name
     * @param array<mixed> $args
     * @return mixed
     */
    protected function callMethod($obj, $name, $args = [])
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

    /**
     * @param mixed $data
     * @return object|array<mixed>
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

    protected function getSchemaObject(?string $schema): object
    {
        $schema = $schema ?? '{}';

        return $this->objectFromJson($schema);
    }

    /**
     * @return object|array<mixed>
     */
    protected function decodeJson(string $json)
    {
        $result = json_decode($json);

        if (null === $result) {
            throw new \InvalidArgumentException('Test not run, not valid json');
        }

        return $result;
    }

    protected function objectFromJson(string $json): object
    {
        $result = json_decode($json);

        if (!is_object($result)) {
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

        return json_encode(json_decode($expected));
    }

    protected function getFixtureFile(string $filename): string
    {
        return file_get_contents($this->getFixturePath($filename));
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
