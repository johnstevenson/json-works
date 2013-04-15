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
        $this->data = Utils::copyData((object) $input, false, array($this, 'init'));
        $this->checkReferences();
    }

    public function find($schema, array $keys)
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

    public function init($data)
    {
        if ($ref = Utils::get($data, '$ref')) {

            if (is_object($data) && is_string($ref)) {
                $this->references[$ref] = null;
            } else {
                throw new \RuntimeException('Invalid reference');
            }
        }

        return $data;
    }

    public function resolve($schema, $parents = array())
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

            $this->data = Utils::copyData($this->data, false, array($this, 'resolve'));
        }
    }
}
