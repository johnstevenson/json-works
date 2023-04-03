<?php declare(strict_types=1);

namespace JohnStevenson\JsonWorks\Schema;

use \stdClass;

use JohnStevenson\JsonWorks\Helpers\Finder;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;
use JohnStevenson\JsonWorks\Helpers\Utils;
use JohnStevenson\JsonWorks\Schema\DataChecker;

class Cache
{
    protected Store $store;
    protected DataChecker $dataChecker;
    protected Finder $finder;

    /** @var array<string> */
    protected array $parents;

    public function __construct(stdClass $schema)
    {
        $this->store = new Store;

        $ref = $this->makeRef('/', '#');
        //$this->store->addRoot('/', $schema, $ref);
        $this->store->addRoot('/', $schema);

        $this->dataChecker = new DataChecker;
        $this->finder = new Finder;
    }

    /**
     * @return mixed
     */
    public function resolveRef(string $ref)
    {
        list($doc, $path) = $this->splitRef($ref);

        if ($this->store->hasRoot($doc)) {
            $this->parents = [];

            $schema = $this->resolve($ref);

            if ($this->dataChecker->checkForRef($schema, $childRef)) {
                $error = $this->getRefError('Circular reference found', $ref);
                throw new \RuntimeException($error);
            }

            return $schema;
        }

        $error = $this->getRefError('Unable to find $ref', $ref);
        throw new \RuntimeException($error);
    }

    /**
     * @return mixed
     */
    protected function resolve(string $ref)
    {
        list($doc, $path) = $this->splitRef($ref);

        $schema = $this->store->get($doc, $path, $data);
        if ($schema !== null) {
            return $schema;
        }

        $this->checkParents($ref);

        $schema = $this->find($ref, $doc, $path, $data);
        if ($schema !== null) {
            return $schema;
        }

        $error = $this->getRefError('Unable to find $ref', $ref);
        throw new \RuntimeException($error);
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function splitRef(string $ref): array
    {
        list($doc, $path) = explode('#', $ref, 2);

        if (Utils::stringIsEmpty($doc)) {
            $doc = '/';
        }

        if (Utils::stringIsEmpty($path)) {
            $path = '#';
        }

        return [$doc, $path];
    }

    protected function checkParents(string $ref): void
    {
        if (!in_array($ref, $this->parents, true)) {
            return;
        }

        $error = $this->getRefError('Circular reference found', $this->parents);
        throw new \RuntimeException($error);
    }

    protected function makeRef(string $doc, string $path): string
    {
        $doc = $doc !== '/' ? $doc : '';

        return sprintf('%s#%s', $doc, $path);
    }

    /**
     * @param mixed $data
     * @return mixed|null
     */
    protected function find(string $ref, string $doc, string $path, $data)
    {
        $error = '';
        $target = new Target($path, $error);

        if ($this->finder->get($data, $target)) {
            return $this->processFoundSchema($ref, $target->element);
        }

        if ($this->dataChecker->checkForRef($target->element, $childRef)) {
            $foundRef = $this->makeRef($doc, $target->foundPath);

            return $this->processFoundRef($ref, $foundRef, $childRef);
        }

        return null;
    }

    /**
     * @param mixed $schema
     * @return mixed
     */
    protected function processFoundSchema(string $ref, $schema)
    {
        if ($this->dataChecker->checkForRef($schema, $childRef)) {
            $this->parents[] = $ref;
            $schema = $this->resolve($childRef);
        }

        $this->addRef($ref, $schema);

        return $schema;
    }

    /**
     * @return mixed|null
     */
    protected function processFoundRef(string $ref, string $foundRef, string $childRef)
    {
        $this->parents[] = $foundRef;
        $schema = $this->resolve($childRef);

        $this->addRef($foundRef, $schema);

        // remove foundRef from parents
        $key = array_search($foundRef, $this->parents, true);
        unset($this->parents[$key]);

        return $this->resolve($ref);
    }

    /**
     * @param mixed $schema
     */
    protected function addRef(string $ref, $schema): void
    {
        list($doc, $path) = $this->splitRef($ref);

        if (!$this->store->add($doc, $path, $schema)) {
            throw new \RuntimeException($this->getRecursionError($ref));
        }
    }

    protected function getRecursionError(string $ref): string
    {
        return $this->getRefError('Recursion searching for $ref', $ref);
    }

    /**
     * @param string|array<string> $ref
     */
    protected function getRefError(string $caption, $ref): string
    {
        return sprintf('%s [%s]', $caption, implode(', ', (array) $ref));
    }
}
