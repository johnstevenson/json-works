<?php declare(strict_types=1);

namespace JohnStevenson\JsonWorks\Schema\Constraint;

use \stdClass;

use JohnStevenson\JsonWorks\Schema\DataChecker;
use JohnStevenson\JsonWorks\Schema\Resolver;

class Manager
{
    /** @var array<string> */
    public array $dataPath;

    /** @var array<string> */
    public array $errors;

    /** @var array<ConstraintInterface> */
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
     * @param stdClass|array<mixed> $schema
     */
    public function validate($data, $schema, ?string $key = null): void
    {
        $schema = $this->setValue($schema);

        if ($this->dataChecker->isEmptySchema($schema)) {
            return;
        }

        $this->dataPath[] = strval($key);

        // Check commmon types first
        if ($this->checkCommonTypes($data, $schema)) {
            $constraint = $this->factory(SpecificConstraint::class);
            $constraint->validate($data, $schema);
        }

        array_pop($this->dataPath);
    }

    /**
     * @template T of ConstraintInterface
     * @param class-string<T> $class
     */
    public function factory(string $class): ConstraintInterface
    {
        if (!isset($this->constraints[$class])) {
            $this->constraints[$class] = new $class($this);
        }

        return $this->constraints[$class];
    }

    /**
    * Fetches a value from the schema
    *
    * @param mixed $value
    * @param array<string>|null $required
    * @throws \RuntimeException
    */
    public function getValue(stdClass $schema, string $key, &$value, ?array $required = null): bool
    {
        $result = property_exists($schema, $key);

        if ($result) {
            $value = $this->setValue($schema->$key);
            $this->dataChecker->checkType($value, $required);
        }

        return $result;
    }

    /**
     * @param mixed $data
     * @param stdClass|array<mixed> $schema
     */
    protected function checkCommonTypes($data, $schema): bool
    {
        $errorCount = count($this->errors);

        $constraint = $this->factory(CommonConstraint::class);
        $constraint->validate($data, $schema);

        return count($this->errors) === $errorCount;
    }

    /**
     * @param stdClass|array<mixed> $schema
     * @return stdClass|array<mixed>
     */
    protected function setValue($schema)
    {
        $ref = $this->dataChecker->checkForRef($schema);

        if ($ref !== null) {
            $schema = $this->resolver->getRef($ref);
            $this->dataChecker->checkType($schema, ['object']);
        }

        return $schema;
    }
}
