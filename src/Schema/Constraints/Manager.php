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
    public $path;
    protected $lax;
    public $errors;
    protected $constraints;
    public $stopOnError;

    public function __construct($lax)
    {
        $this->lax = $lax;
        $this->tokenizer = new Tokenizer();
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
        $common = array('enum', 'type', 'allOf', 'anyOf', 'oneOf', 'not');

        foreach ($common as $name) {

            if ($subSchema = $this->get($schema, $name)) {
                $this->check($name, [$data, $subSchema]);
            }
        }

        return $errors === count($this->errors);
    }

    protected function validateInstance($data, $schema)
    {
        if (!$name = $this->getInstanceName($data)) {
            return;
        }

        switch ($name) {
            case 'object':
                $this->validateObject($data, $schema);
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
        $result = '';

        if (is_null($data) || is_bool($data)) {
            return $result;
        }

        $result = gettype($data);

        if (in_array($result, ['double', 'integer'])) {
            $result = 'number';
        }

        return $result;
    }

    protected function validateObject($data, $schema)
    {

        # maxProperties
        if (isset($schema->maxProperties)) {
            $this->validateMaxMin($data, $schema->maxProperties, true);
        }

        if (!$this->lax) {

            # minProperties
            if (isset($schema->minProperties)) {
                $this->validateMaxMin($data, $schema->minProperties, false);
            }

            if (isset($schema->required)) {
                foreach ((array) $schema->required as $name) {
                    if (!isset($data->$name)) {
                        $this->throwError(sprintf("is missing required property '%s'", $name));
                    }
                }
            }

        }

        # additionalProperties
        $additional = Utils::get($schema, 'additionalProperties', true);

        if (false === $additional) {
            $this->validateObjectWork($data, $schema);
        }

        $this->validateObjectChildren($data, $schema, $additional);
    }

    protected function validateMaxMin($data, $value, $isMax)
    {
        $count = count((array) $data);

        if ($isMax && $count > $value) {
            $error = 'has too many members, maximum %d';
        } elseif (!$isMax && $count < $value) {
            $error = 'has too few members, minimum (%d)';
        }

        if (isset($error)) {
            $this->throwError(sprintf($error, $value));
        }
    }

    protected function validateObjectWork($data, $schema)
    {
        $set = (array) $data;
        $p = Utils::get($schema, 'properties', new \stdClass());

        foreach ($p as $key => $value) {
            if (isset($set[$key])) {
                unset($set[$key]);
            }
        }

        $pp = Utils::get($schema, 'patternProperties', new \stdClass());
        $setCopy = $set;

        foreach ($setCopy as $key => $value) {

            foreach ($pp as $regex => $val) {
                if ($this->match($regex, $key)) {
                    unset($set[$key]);
                    break;
                }
            }
        }

        if (!empty($set)) {
            $this->throwError('contains unspecified additional properties');
        }
    }

    protected function validateObjectChildren($data, $schema, $additional)
    {
        if (true === $additional) {
            $additional = new \stdClass();
        }

        $p = Utils::get($schema, 'properties', new \stdClass());
        $pp = Utils::get($schema, 'patternProperties', new \stdClass());

        foreach ($data as $key => $value) {

            $child = array();

            if (isset($p->$key)) {
                $child[] = $p->$key;
            }

            foreach ($pp as $regex => $val) {
                if ($this->match($regex, $key)) {
                    $child[] = $val;
                }
            }

            if (empty($child) && $additional) {
                $child[] = $additional;
            }

            foreach ($child as $subSchema) {
                $this->validateChild($value, $subSchema, $key);
            }
        }
    }

    protected function validateUnique($data)
    {
        $count = count($data);
        for ($i = 0; $i < $count; ++$i) {
            for ($j = $i + 1; $j < $count; ++$j) {
                if (Utils::equals($data[$i], $data[$j])) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function match($regex, $string)
    {
         return preg_match('/'.$regex.'/', $string, $match);
    }

    protected function throwError($msg)
    {
        $path = $this->path ?: '#';
        $error = sprintf("Property: '%s'. Error: %s", $path, $msg);
        throw new ValidationException($error);
    }
}
