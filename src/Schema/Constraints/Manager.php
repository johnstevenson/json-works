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

        if ($this->validateCommon($data, $schema)) {
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

    public function getValue($schema, $key, &$value, &$type)
    {
        if (!is_object($schema)) {
            $this->throwSchemaError($schema, 'object');
        }

        if ($result = property_exists($schema, $key)) {
            $value = $schema->$key;
            $type = gettype($value);
        }

        return $result;
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

    public function throwSchemaError($expected, $value)
    {
        $error = sprintf(
            "Invalid schema value: expected '%s', got '%s'",
            $expected,
            $value
        );

        throw new \RuntimeException($error);
    }

    protected function init($schema, $key)
    {
        if (is_null($schema)) {
            throw new \RuntimeException('Schema is null');
        }

        if ($result = count(get_object_vars($schema) > 0)) {
            $this->path = $this->tokenizer->add($this->path, $key);
        }

        return $result;
    }

    protected function validateCommon($data, $schema)
    {
        $errors = count($this->errors);
        $common = ['enum', 'type', 'allOf', 'anyOf', 'oneOf', 'not'];

        foreach ($common as $key) {

            if ($subSchema = $this->get($schema, $key)) {
                $name = preg_match('/(?:Of|not)$/', $key) ? 'of' : $key;
                $this->check($name, [$data, $subSchema, $key]);
            }
        }

        return $errors === count($this->errors);
    }

    protected function validateInstance($data, $schema)
    {
        if ($name = $this->getInstanceName($data)) {
            $this->check($name, [$data, $schema]);
        }

        return;

        switch ($name) {
            case 'object':
                //$this->validateObject($data, $schema);
                $this->check($name, [$data, $schema]);
                break;
            case 'array':
                $this->check($name, [$data, $schema]);
                break;
            //case 'double':
            //    # no break
            case 'number':
                $this->check($name, [$data, $schema]);
                break;
            case 'string':
                $this->check($name, [$data, $schema]);
                break;
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
}
