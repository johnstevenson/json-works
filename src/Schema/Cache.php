<?php

namespace JohnStevenson\JsonWorks\Schema;

use JohnStevenson\JsonWorks\Helpers\Finder;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;
use JohnStevenson\JsonWorks\Schema\DataChecker;

class Cache
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
    * @var Store
    */
    protected $store;

    /**
    * @var array
    */
    protected $parents;

    public function __construct($schema)
    {
        $this->store = new Store;

        $ref = $this->makeRef('/', '#');
        $this->store->addRoot('/', $schema, $ref);

        $this->dataChecker = new DataChecker;
        $this->finder = new Finder;
    }

    public function resolveRef($ref)
    {
        $this->splitRef($ref, $doc, $path);

        if ($this->store->hasRoot($doc)) {
            $this->parents = [];

            $schema = $this->resolve($ref);

            if ($this->dataChecker->checkForRef($schema, $childRef)) {
                $error = $this->getRefError('Circular reference found', $ref);
                throw new \RuntimeException($error);
            }

            return $schema;
        }
    }

    protected function resolve($ref)
    {
        $this->splitRef($ref, $doc, $path);

        if ($schema = $this->store->get($doc, $path, $data)) {
            return $schema;
        }

        $this->checkParents($ref);

        if ($schema = $this->find($ref, $doc, $path, $data)) {
            return $schema;
        }

        $error = $this->getRefError('Unable to find $ref', $ref);
        throw new \RuntimeException($error);
    }

    protected function splitRef($ref, &$doc, &$path)
    {
        $parts = explode('#', $ref, 2);

        $doc = $parts[0] ?: '/';
        $path = $parts[1] ?: '#';
    }

    protected function checkParents($ref)
    {
        if (!in_array($ref, $this->parents)) {
            return;
        }

        $error = $this->getRefError('Circular reference found', $this->parents);
        throw new \RuntimeException($error);
    }

    protected function makeRef($doc, $path)
    {
        $doc = $doc !== '/' ? $doc : '';

        return sprintf('%s#%s', $doc, $path);
    }

    protected function find($ref, $doc, $path, $data)
    {
        $target = new Target($path, $error);

        if ($this->finder->get($data, $target)) {
            return $this->processFoundSchema($ref, $target->element);
        }

        if ($this->dataChecker->checkForRef($target->element, $childRef)) {
            $foundRef = $this->makeRef($doc, $target->foundPath);

            return $this->processFoundRef($ref, $foundRef, $childRef);
        }
    }

    protected function processFoundSchema($ref, $schema)
    {
        if ($this->dataChecker->checkForRef($schema, $childRef)) {
            $this->parents[] = $ref;
            $schema = $this->resolve($childRef);
        }

        $this->addRef($ref, $schema);

        return $schema;
    }

    protected function processFoundRef($ref, $foundRef, $childRef)
    {
        $this->parents[] = $foundRef;

        if ($schema = $this->resolve($childRef)) {
            $this->addRef($foundRef, $schema);

            // remove foundRef from parents
            $key = array_search($foundRef, $this->parents);
            unset($this->parents[$key]);

            return $this->resolve($ref);
        }
    }

    protected function addRef($ref, $schema)
    {
        $this->splitRef($ref, $doc, $path);

        if (!$this->store->add($doc, $path, $schema)) {
            throw new \RuntimeException($this->getRecursionError($ref));
        }
    }

    protected function getRecursionError($ref)
    {
        return $this->getRefError('Recursion searching for $ref', $ref);
    }

    protected function getRefError($caption, $ref)
    {
        return sprintf('%s [%s]', $caption, implode(', ', (array) $ref));
    }
}
