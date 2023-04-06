<?php declare(strict_types=1);

namespace JohnStevenson\JsonWorks\Schema\Constraint;

use \stdClass;

use JohnStevenson\JsonWorks\Schema\DataChecker;
use JohnStevenson\JsonWorks\Schema\Resolver;

class Manager
{
    /** @var array<mixed> */
    public array $dataPath;

    /** @var array<string> */
    public array $errors;

    /** @var array<object> */
    protected $constraints;

    public bool $stopOnError;
    public DataChecker $dataChecker;

    protected Resolver $resolver;

    public function __construct(Resolver $resolver, bool $stopOnError)
    {
        $this->resolver = $resolver;
        $this->stopOnError = $stopOnError;

        $this->dataPath = [];
        $this->errors = [];
        $this->constraints = [];
        $this->dataChecker = new DataChecker();
    }

    /**
     * @param mixed $data
     * @param mixed $schema
     */
    public function validate($data, $schema, ?string $key = null): void
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

    public function factory(string $name): object
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
    * @param mixed $value
    * @param mixed|null $required
    * @throws \RuntimeException
    */
    public function getValue($schema, string $key, &$value, $required = null): bool
    {
        if (is_object($schema)) {

            if ($result = property_exists($schema, $key)) {
                // @phpstan-ignore-next-line
                $value = $this->setValue($schema->$key);
                $this->dataChecker->checkType($value, $required);
            }

            return $result;
        }

        $error = $this->dataChecker->formatError('object', gettype($schema));
        throw new \RuntimeException($error);
    }

    /**
     * @param mixed $schema
     * @return mixed
     */
    protected function setValue($schema)
    {
        if ($this->dataChecker->checkForRef($schema, $ref)) {
            $schema = $this->resolver->getRef($ref);
            $this->dataChecker->checkType($schema, 'object');
        }

        return $schema;
    }
}
