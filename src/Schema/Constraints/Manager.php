<?php

namespace JohnStevenson\JsonWorks\Schema\Constraints;

use JohnStevenson\JsonWorks\Schema\ValidationException;

class Manager
{
    public $path;
    public $dataPath;
    protected $lax;
    public $errors;
    protected $constraints;
    public $stopOnError;

    public function __construct($lax)
    {
        $this->lax = $lax;

        $this->dataPath = [];
        $this->errors = [];
        $this->constraints = [];
        $this->stopOnError = false;
    }

    public function validate($data, $schema, $key = null)
    {
        if (!$this->init($schema, $key)) {
            return;
        }

        if ($this->check('common', [$data, $schema])) {
            $this->check('instance', [$data, $schema]);
        }

        if ($key) {
            array_pop($this->dataPath);
        }
    }

    public function testChild($data, $schema)
    {
        $currentStop = $this->stopOnError;
        $this->stopOnError = true;

        try {
            $this->validate($data, $schema);
            $result = true;
        } catch (ValidationException $e) {
            $result = false;
            array_pop($this->errors);
        }

        $this->stopOnError = $currentStop;
        return $result;
    }

    public function check($name, array $params)
    {
        if (!isset($this->constraints[$name])) {
            $class = sprintf('\%s\%sConstraint', __NAMESPACE__, ucfirst($name));
            $this->constraints[$name] = new $class($this);
        }

        return call_user_func_array([$this->constraints[$name], 'check'], $params);
    }

    /**
    * Fetches a value from the schema
    *
    * @param mixed $schema
    * @param mixed $key
    * @param mixed $value
    * @param string $type Set by method
    * @param mixed $required
    * @throws RuntimeException
    */
    public function getValue($schema, $key, &$value, &$type, $required = null)
    {
        if (is_object($schema)) {

            if ($result = property_exists($schema, $key)) {
                $value = $schema->$key;
                $type = $this->checkType($value, $required);
            }

            return $result;
        }

        $error = $this->getSchemaError('object', gettype($schema));
        throw new \RuntimeException($error);
    }

    public function get($schema, $key, $default = null)
    {
        if ($this->getValue($schema, $key, $value, $type)) {
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

    protected function init($schema, $key)
    {
        if (!is_object($schema)) {
            $error = $this->getSchemaError('object', gettype($schema));
            throw new \RuntimeException($error);
        }

        if ($result = count((array) $schema) > 0) {

            if ($key) {
                $this->dataPath[] = $key;
            }
        }

        return $result;
    }

    protected function checkType($value, $required)
    {
        $result = gettype($value);

        if ($required !== null) {

            $types = (array) $required;

            if (!in_array($result, $types)) {
                $error = $this->getSchemaError(implode($types), $result);
                throw new \RuntimeException($error);
            }
        }

        return $result;
    }
}
