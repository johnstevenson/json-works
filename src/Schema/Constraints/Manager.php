<?php

namespace JohnStevenson\JsonWorks\Schema\Constraints;

use JohnStevenson\JsonWorks\Utils;
use JohnStevenson\JsonWorks\Helpers\Tokenizer;
use JohnStevenson\JsonWorks\Schema\ValidationException;

class Manager
{
    /**
    * @var \JohnStevenson\JsonWorks\Helpers\Tokenizer
    */
    protected $tokenizer;

    /**
    * @var JsonTypes
    */
    public $jsonTypes;

    public $path;
    protected $lax;
    public $errors;
    protected $constraints;
    public $stopOnError;

    public function __construct($lax)
    {
        $this->lax = $lax;
        $this->tokenizer = new Tokenizer();
        $this->jsonTypes = new JsonTypes();
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
            $this->validateInstance($data, $schema);
        }
    }

    public function validateChild($data, $schema, $key = null)
    {
        $result = true;
        $currentPath = $this->path;
        $currentStop = $this->stopOnError;

        if (is_null($key)) {
            try {
                $this->stopOnError = true;
                $this->validate($data, $schema);
            } catch (ValidationException $e) {
                $result = false;
                array_pop($this->errors);
            }
        } else {
            $this->validate($data, $schema, $key);
        }

        $this->path = $currentPath;
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
                $type = $this->checkType($type, $value, $required);
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

    public function addError($message)
    {
        $path = $this->path ?: '#';
        $this->errors[] = sprintf("Property: '%s'. Error: %s", $path, $message);

        if ($this->stopOnError) {
            throw new ValidationException();
        }
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
        if (is_object($schema)) {

            if ($result = count((array) $schema) > 0) {
                $this->path = $this->tokenizer->add($this->path, $key);
            }

            return $result;
        }

        $error = $this->getSchemaError('object', gettype($schema));
        throw new \RuntimeException($error);
    }

    protected function validateInstance($data, $schema)
    {
        if ($name = $this->getInstanceName($data)) {
            $this->check($name, [$data, $schema]);
        }
    }

    protected function getInstanceName($data)
    {
        $result = $this->jsonTypes->getGeneric($data);

        if (in_array($result, ['boolean', 'null'])) {
            $result = '';
        }

        return $result;
    }

    protected function checkType($type, $value, $required)
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
