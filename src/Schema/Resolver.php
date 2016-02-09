<?php

namespace JohnStevenson\JsonWorks\Schema;

use JohnStevenson\JsonWorks\Helpers\Finder;
use JohnStevenson\JsonWorks\Schema\DataChecker;

class Resolver
{
    /**
    * @var DataChecker
    */
    protected $dataChecker;

    /**
    * @var \JohnStevenson\JsonWorks\Helpers\Finder
    */
    protected $finder;

    /**
    * @var array
    */
    protected $refs = [];

    public function __construct($schema)
    {
        $this->dataChecker = new DataChecker;
        $this->finder = new Finder;

        $this->addRef('/', '#', $schema);
    }

    public function getRef($ref)
    {
        return $this->getReference($ref);
    }

    protected function addRef($doc, $path, $schema)
    {
        if (!isset($this->refs[$doc])) {
            $this->refs[$doc] = [];
        }

        if (!isset($this->refs[$doc][$path])) {
            $this->refs[$doc][$path] = $schema;
        }
    }

    protected function getReference($ref, $parents = [])
    {
        if ($schema = $this->checkRef($ref, $doc, $path)) {
            return $schema;
        }

        if (in_array($ref, $parents)) {
            throw new \RuntimeException('Circular reference to $ref '.$ref);
        }
        $data = $this->refs[$doc]['#'];

        if ($this->finder->find($path, $data, $schema, $error)) {

            if ($this->dataChecker->checkForRef($schema, $childRef)) {
                $parents[] = $ref;
                $schema = $this->getReference($childRef, $parents);
            }

            $this->addRef($doc, $path, $schema);

            return $schema;
        }

        throw new \RuntimeException('Unable to find $ref '.$ref);
    }

    protected function checkRef($ref, &$doc, &$path)
    {
        $parts = explode('#', $ref, 2);

        $doc = $parts[0] ?: '/';
        $path = $parts[1] ?: '#';

        if (empty($this->refs[$doc])) {
            // fetch and load the data
        } elseif (!empty($this->refs[$doc][$path])) {
            return $this->refs[$doc][$path];
        }
    }
}
