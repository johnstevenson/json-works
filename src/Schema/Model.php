<?php

namespace JohnStevenson\JsonWorks\Schema;

use JohnStevenson\JsonWorks\Utils;
use JohnStevenson\JsonWorks\Helpers\Formatter;
use JohnStevenson\JsonWorks\Helpers\Tokenizer;

class Model
{
    public $data;

    /**
    * @var array
    */
    protected $references = array();

    /**
    * @var \JohnStevenson\JsonWorks\Helpers\Formatter
    */
    protected $formatter;

    /**
    * @var \JohnStevenson\JsonWorks\Helpers\Tokenizer
    */
    protected $tokenizer;

    public function __construct($input)
    {
        $this->formatter = new Formatter();
        $this->tokenizer = new Tokenizer();

        $this->data = $this->formatter->copy((object) $input, array($this, 'initCallback'));
        $this->resolveReferences();
    }

    public function find($schema, array $keys)
    {
        while (!empty($keys) && $schema) {
            $type = gettype($schema);
            $key = array_shift($keys);

            if ('array' === $type) {
                $key = (int) $key;
            }
            $schema = Utils::get($schema, $key);
        }

        return $schema;
    }

    public function initCallback($data)
    {
        if ($ref = Utils::get($data, '$ref')) {

            if (is_string($ref) && 0 === strpos($ref, '#')) {
                $this->references[$ref] = null;
            } else {
                throw new \RuntimeException('Invalid reference '.$ref);
            }
        }

        return $data;
    }

    public function resolveCallback($data)
    {
        if ($ref = Utils::get($data, '$ref')) {
            $data = Utils::get($this->references, $ref);
        }

        return $data;
    }

    protected function resolveReferences()
    {
        if (!empty($this->references)) {

            $this->getReferences();

            foreach ($this->references as $ref => $schema) {
                $this->references[$ref] = $this->resolve($schema);
            }

            $this->data = $this->formatter->copy($this->data, array($this, 'resolveCallback'));
            $this->references = array();
        }
    }

    protected function getReferences()
    {
        foreach (array_keys($this->references) as $ref) {

            if (!$this->tokenizer->decode(substr($ref, 1), $keys)) {
                throw new \RuntimeException('Invalid reference '.$ref);
            }

            if ($schema = $this->find($this->data, $keys)) {
                $this->references[$ref] = $schema;
            } else {
                throw new \RuntimeException('Unable to find ref '.$ref);
            }
        }
    }

    protected function resolve($schema, $parents = array())
    {
        $result = $schema;

        if ($ref = Utils::get($schema, '$ref')) {
            $refSchema = Utils::get($this->references, $ref);

            if (in_array($ref, $parents)) {
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
