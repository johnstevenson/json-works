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

    protected function callMethod($obj, $name, $args = array())
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

    protected function getSchemaObject($schema)
    {
        $schema = $schema ?: '{}';

        if (is_string($schema)) {
            $schema = trim($schema);
        }

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

    protected function getFileExpectedJson($filename, $tabs = false)
    {
        if ($tabs) {
            $data = file($filename, FILE_IGNORE_NEW_LINES);
            return $this->fileSpacesToTabs($data);
         } else {
            return file_get_contents($filename);
        }
    }

    protected function fileSpacesToTabs($data)
    {
        $space = str_repeat(chr(32), 4);

        foreach($data as &$line)
        {
            $tabs = '';

            while (0 === strpos($line, $space)) {
                $line = substr($line, 4);
                $tabs .= "\t";
            }

            $line = $tabs.$line;
        }

        return implode("\n", $data)."\n";
    }
}
