<?php

namespace JsonWorks\Tests;

class Base extends \PHPUnit_Framework_TestCase
{
    public function validate($schema, $data)
    {
        $schema = json_decode(trim($schema));

        if (null === $schema) {
            throw new \InvalidArgumentException('Test not run, $schema not valid json');
        }

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

    public function getDocument($schema, $data)
    {

        $schema = $schema ?: '{}';
        $schema = json_decode(trim($schema));

        if (null === $schema) {
            throw new \InvalidArgumentException('Test not run, $schema not valid json');
        }

        $data = $data ?: null;

        if ($data) {
            $data = json_decode($data);
            if (null === $data) {
                throw new \InvalidArgumentException('Test not run, $data not valid json');
            }
        }

        return new \JohnStevenson\JsonWorks\Document($data, $schema);
    }

    public function callMethod($obj, $name, $args = array())
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }
}
