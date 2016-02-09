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

    /**
    * @var array
    */
    protected $parents;

    public function __construct($schema)
    {
        $this->dataChecker = new DataChecker;
        $this->finder = new Finder;

        $this->addRef('/', '#', $schema);
    }

    public function getRef($ref)
    {
        $this->parents = [];

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

    protected function getReference($ref)
    {
        if ($schema = $this->checkRef($ref, $doc, $path)) {
            return $schema;
        }

        if (in_array($ref, $this->parents)) {
            throw new \RuntimeException('Circular reference to $ref '.$ref);
        }

        if ($schema = $this->find($ref, $doc, $path)) {
            return $schema;
        }

        throw new \RuntimeException('Unable to find $ref '.$ref);
    }

    protected function checkRef($ref, &$doc, &$path)
    {
        $this->splitRef($ref, $doc, $path);

        if (empty($this->refs[$doc])) {
            // fetch and load the data
        } elseif (!empty($this->refs[$doc][$path])) {
            return $this->refs[$doc][$path];
        }
    }

    protected function splitRef($ref, &$doc, &$path)
    {
        $parts = explode('#', $ref, 2);

        $doc = $parts[0] ?: '/';
        $path = $parts[1] ?: '#';
    }

    protected function find($ref, $doc, $path)
    {
        $data = $this->refs[$doc]['#'];

        if ($this->finder->find($path, $data, $schema, $error)) {

            if ($this->dataChecker->checkForRef($schema, $childRef)) {
                $this->parents[] = $ref;
                $schema = $this->getReference($childRef);
            }

            $this->addRef($doc, $path, $schema);

            return $schema;
        }
    }
}
