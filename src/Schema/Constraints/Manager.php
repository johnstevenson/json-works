<?php

namespace JohnStevenson\JsonWorks\Schema\Constraints;

use JohnStevenson\JsonWorks\Schema\DataChecker;

class Manager
{
    public $dataPath;
    public $errors;
    public $stopOnError;
    public $dataChecker;

    protected $constraints;

    public function __construct()
    {
        $this->dataPath = [];
        $this->errors = [];
        $this->constraints = [];
        $this->stopOnError = false;
        $this->dataChecker = new DataChecker($this);
    }

    public function validate($data, $schema, $key = null)
    {
        if ($this->dataChecker->emptySchema($schema)) {
            return;
        }

        $this->dataPath[] = strval($key);

        // Check commmon types first
        $common = $this->factory('common');

        if ($common->validate($data, $schema)) {

            $specific = $this->factory('specific');
            $specific->validate($data, $schema);
        }

        array_pop($this->dataPath);
    }

    public function factory($name)
    {
        if (!isset($this->constraints[$name])) {
            $class = sprintf('\%s\%sConstraint', __NAMESPACE__, ucfirst($name));
            $this->constraints[$name] = new $class($this);
        }

        return $this->constraints[$name];
    }

    /**
    * Fetches a value from the schema
    *
    * @param mixed $schema
    * @param mixed $key
    * @param mixed $value
    * @param mixed $required
    * @throws RuntimeException
    */
    public function getValue($schema, $key, &$value, $required = null)
    {
        if (is_object($schema)) {

            if ($result = property_exists($schema, $key)) {
                $value = $schema->$key;
                $this->dataChecker->checkType($value, $required);
            }

            return $result;
        }

        $error = $this->getSchemaError('object', gettype($schema));
        throw new \RuntimeException($error);
    }

    public function get($schema, $key, $default = null)
    {
        if ($this->getValue($schema, $key, $value)) {
            return $value;
        }

        return $default;
    }

    public function getSchemaError($expected, $value)
    {
        return sprintf(
            "Invalid schema value: expected '%s', got '%s'",
            $expected,
            $value
        );
    }
}
