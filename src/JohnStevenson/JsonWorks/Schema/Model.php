<?php

namespace JohnStevenson\JsonWorks\Schema;

use \JohnStevenson\JsonWorks\Utils as Utils;

class Model
{
    public $data;
    /**
    * @var array
    */
    protected $references = array();

    public function __construct($input)
    {
        $this->data = $this->parse((object) $input, 'addReference');
        $this->checkReferences();
    }

    public function parse($input, $callback)
    {
        if ($object = is_object($input)) {
            if ($ref = Utils::get($input, '$ref')) {
                return $this->$callback($input);
            }
        }

        if ($object || is_array($input)) {
            $result = array();

            foreach ($input as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $value = $this->parse($value, $callback);
                }
                $result[$key] = $value;
            }

            if ($object) {
                $result = (object) $result;
            }

        } else {
            $result = $input;
        }

        return $result;
    }

    protected function addReference($schema)
    {
        if (is_string($schema->{'$ref'})) {
            $ref = trim($schema->{'$ref'});

            if (0 === strpos($ref, '#')){
                $schema->{'$ref'} = $ref;
                $this->references[$ref] = null;
                return $schema;
            }
        }

        throw new \RuntimeException('Invalid reference');
    }

    protected function checkReferences()
    {
        if ($this->references) {

            foreach (array_keys($this->references) as $ref) {
                $keys = Utils::decodePath($ref);

                if ($schema = $this->find($this->data, $keys)) {
                    $this->references[$ref] = $schema;
                } else {
                    throw new \RuntimeException('Unable to resolve ref '.$ref);
                }
            }

            foreach ($this->references as $ref => $schema) {
                $this->references[$ref] = $this->resolve($schema);
            }

            $this->data = $this->parse($this->data, 'resolve');
        }
    }

    protected function find($schema, array $keys)
    {
        while ($keys && $schema) {
            $type = gettype($schema);

            $key = array_shift($keys);
            if ('array' === $type) {
                $key = (int) $key;
            }

            $schema = Utils::get($schema, $key);
         }

        return $schema;
    }

    protected function resolve($schema, $parents = array())
    {
        $result = $schema;

        if ($ref = Utils::get($schema, '$ref')) {

            if (!$refSchema = Utils::get($this->references, $ref)) {
                throw new \RuntimeException('Unable to find ref '.$ref);
            } elseif ($refSchema === $schema || in_array($ref, $parents)) {
                throw new \RuntimeException('Circular reference to ref '.$ref);
            } elseif (Utils::get($refSchema, '$ref')) {
                $parents[] = $ref;
                $result = $this->resolve($refSchema, $parents);
            } else {
                $result = $refSchema;
            }
        }

        return $result;
    }
}
