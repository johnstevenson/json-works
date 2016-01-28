<?php

namespace JsonWorks\Tests;

class Base extends \PHPUnit_Framework_TestCase
{
    protected function validate($schema, $data)
    {
        $schema = $this->getSchemaObject($schema);

        if (is_string($data)) {

            $data = trim($data);

            if (preg_match('#^\{(.*)\}$#s', $data) || preg_match('#^\[(.*)\]$#s', $data)) {
                $data = json_decode($data);
                if (null === $data) {
                    throw new \InvalidArgumentException('Test not run, $data not valid json');
                }
            }
        }

        $schema = new \JohnStevenson\JsonWorks\Schema\Model($schema);
        $validator = new \JohnStevenson\JsonWorks\Schema\Validator();
        return $validator->check($data, $schema);
    }

    protected function getDocument($schema, $data, $noException = false)
    {
        $schema = $this->getSchemaObject($schema);
        $data = $data ?: null;

        if ($data) {
            $data = json_decode($data);
            if (null === $data) {
                throw new \InvalidArgumentException('Test not run, $data not valid json');
            }
        }

        $document = new \JohnStevenson\JsonWorks\Document();
        $document->loadData($data, $noException);
        $document->loadSchema($schema, $noException);
        return $document;
    }

    protected function getSchema($schema)
    {
        $schema = $this->getSchemaObject($schema);
        $schema = new \JohnStevenson\JsonWorks\Schema\Model($schema);

        return $schema->data;
    }

    protected function callMethod($obj, $name, $args = [])
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

    protected function getSchemaObject($schema)
    {
        $schema = $schema ?: '{}';

        return $this->fromJson($schema);
    }

    protected function fromJson($json)
    {
        $json = json_decode($json);

        if (null === $json) {
            throw new \InvalidArgumentException('Test not run, not valid json');
        }

        return $json;
    }

    protected function getExpectedJson($expected)
    {
        return json_encode(json_decode($expected));
    }

    protected function getFixtureFile($filename)
    {
        return file_get_contents($this->getFixturePath($filename));
    }

    protected function getFixturePath($filename)
    {
        return __DIR__.'/Fixtures/'.$filename;
    }

    protected function sameRef(&$var1, &$var2)
    {
        if ($var1 !== $var2) {
            return false;
        }
        // backup $var1
        $tmp = $var1;

        $var1 = !$var1;
        $result = $var1 === $var2;

        // restore $var1
        $var1 = $tmp;

        return $result;
    }
}
