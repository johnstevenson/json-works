<?php

namespace JohnStevenson\JsonWorks\Schema\Constraints;

use JohnStevenson\JsonWorks\Schema\DataChecker;
use JohnStevenson\JsonWorks\Schema\Resolver;

class Manager
{
    public $dataPath;
    public $errors;
    public $stopOnError;
    public $dataChecker;

    protected $constraints;
    protected $resolver;

    public function __construct(Resolver $resolver, $stopOnError)
    {
        $this->resolver = $resolver;
        $this->stopOnError = $stopOnError;

        $this->dataPath = [];
        $this->errors = [];
        $this->constraints = [];
        $this->dataChecker = new DataChecker();
    }

    public function validate($data, $schema, $key = null)
    {
        $schema = $this->setValue($schema);

        if ($this->dataChecker->emptySchema($schema)) {
            return;
        }

        $this->dataPath[] = strval($key);

        // Check commmon types first
        $common = $this->factory('common');

        if ($common->validate($data, $schema)) {

            $specific = $this->factory('specific');
            $specific->validate($data, $schema);
        }

        array_pop($this->dataPath);
    }

    public function factory($name)
    {
        if (!isset($this->constraints[$name])) {
            $class = sprintf('\%s\%sConstraint', __NAMESPACE__, ucfirst($name));
            $this->constraints[$name] = new $class($this);
        }

        return $this->constraints[$name];
    }

    /**
    * Fetches a value from the schema
    *
    * @param mixed $schema
    * @param mixed $key
    * @param mixed $value
    * @param mixed $required
    * @throws RuntimeException
    */
    public function getValue($schema, $key, &$value, $required = null)
    {
        if (is_object($schema)) {

            if ($result = property_exists($schema, $key)) {
                $value = $this->setValue($schema->$key);
                $this->dataChecker->checkType($value, $required);
            }

            return $result;
        }

        $error = $this->dataChecker->formatError('object', gettype($schema));
        throw new \RuntimeException($error);
    }

    protected function setValue($schema)
    {
        if ($this->dataChecker->checkForRef($schema, $ref)) {
            $schema = $this->resolver->getRef($ref);
            $this->dataChecker->checkType($schema, 'object');
        }

        return $schema;
    }
}
