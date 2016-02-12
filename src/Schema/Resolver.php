<?php

namespace JohnStevenson\JsonWorks\Schema;

use JohnStevenson\JsonWorks\Helpers\Finder;
use JohnStevenson\JsonWorks\Helpers\Loader;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;
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

    protected $loader;

    /**
    * @var array
    */
    protected $refs = [];

    /**
    * @var array
    */
    protected $parents;

    public function __construct(Loader $loader, $schema, $basePath)
    {
        $this->loader = $loader;
        $this->dataChecker = new DataChecker;
        $this->finder = new Finder;

        $this->addRef('#', $schema);
    }

    public function getRef($ref)
    {
        $this->parents = [];

        return $this->getReference($ref);
    }

    protected function addRef($ref, $schema)
    {
        $this->splitRef($ref, $doc, $path);

        if (!isset($this->refs[$doc])) {
            $this->refs[$doc] = [];
        }

        if (array_key_exists($path, $this->refs[$doc])) {
            $error = $this->getRefError('Recursion searching for $ref', $ref);
            throw new \RuntimeException($error);
        }

        foreach ($this->refs[$doc] as $key => $dummy) {
            if (0 === strpos($path, $key)) {
                return;
            }
        }

        $this->refs[$doc][$path] = $schema;
    }

    protected function getReference($ref)
    {
        if ($schema = $this->checkRef($ref, $doc, $path, $docKey)) {
            return $schema;
        }

        $this->checkParents($ref);

        if ($schema = $this->find($ref, $doc, $path, $docKey)) {
            return $schema;
        }

        $error = $this->getRefError('Unable to find $ref', $ref);
        throw new \RuntimeException($error);
    }

    protected function checkRef($ref, &$doc, &$path, &$docKey)
    {
        $this->splitRef($ref, $doc, $path);

        if (empty($this->refs[$doc])) {
            // fetch and load the data
        } elseif (!empty($this->refs[$doc][$path])) {
            return $this->refs[$doc][$path];
        } else {
            $docKey = '#';
            foreach ($this->refs[$doc] as $partPath => $nothing) {

                if (0 === strpos($path, $partPath)) {
                    $docKey = $partPath;
                    $path = substr($path, strlen($partPath));
                    break;
                }
            }
        }
    }

    protected function checkParents($ref)
    {
        if (!in_array($ref, $this->parents)) {
            return;
        }

        $error = $this->getRefError('Circular reference found', $this->parents);
        throw new \RuntimeException($error);
    }

    protected function splitRef($ref, &$doc, &$path)
    {
        $parts = explode('#', $ref, 2);

        $doc = $parts[0] ?: '/';
        $path = $parts[1] ?: '#';
    }

    protected function makeRef($doc, $path)
    {
        $doc = $doc !== '/' ? $doc : '';

        return sprintf('%s#%s', $doc, $path);
    }

    protected function removeFromParent($ref)
    {
        $key = array_search($ref, $this->parents);

        if ($key !== false) {
            unset($this->parents[$key]);
        }
    }

    protected function find($ref, $doc, $path, $docKey)
    {
        $data = $this->refs[$doc][$docKey];
        $target = new Target($path, $error);

        if ($this->finder->get($data, $target)) {
            return $this->processFoundSchema($ref, $target->element);
        }

        if ($this->dataChecker->checkForRef($target->element, $childRef)) {

            $foundPath = $this->makeRef($doc, $target->foundPath);
            $this->parents[] = $foundPath;

            if ($schema = $this->getReference($childRef)) {
                $this->removeFromParent($foundPath);
                $this->addRef($foundPath, $schema);

                return $this->getReference($ref);
            }
        }
    }

    protected function processFoundSchema($ref, $schema)
    {
        if ($this->dataChecker->checkForRef($schema, $childRef)) {
            $this->parents[] = $ref;
            $schema = $this->getReference($childRef);
        }

        $this->addRef($ref, $schema);

        return $schema;
    }

    protected function getRefError($caption, $ref)
    {
        return sprintf('%s [%s]', $caption, implode(', ', (array) $ref));
    }
}
