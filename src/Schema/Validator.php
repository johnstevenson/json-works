<?php declare(strict_types=1);

namespace JohnStevenson\JsonWorks\Schema;

use stdClass;

use JohnStevenson\JsonWorks\BaseDocument;
use JohnStevenson\JsonWorks\Helpers\Loader;
use JohnStevenson\JsonWorks\Helpers\Utils;
use JohnStevenson\JsonWorks\Schema\Resolver;
use JohnStevenson\JsonWorks\Schema\ValidationException;
use JohnStevenson\JsonWorks\Schema\Constraint\Manager;

class Validator
{
    /** @var array<string> */
    protected array $errors = [];
    protected stdClass $schema;
    protected bool $stopOnError = false;

    protected Loader $loader;
    protected Resolver $resolver;

    /**
     * @param mixed $schema
     */
    public function __construct($schema)
    {
        $this->loader = new Loader;
        $this->schema = $this->loader->loadSchema($schema);
        $this->resolver = new Resolver($this->schema);
    }

    /**
     * @param mixed $data
     */
    public function check($data): bool
    {
        $data = $this->getData($data);
        $manager = new Manager($this->resolver, $this->stopOnError);

        try {

            $manager->validate($data, $this->schema);
        } catch (ValidationException $e) {
            // The exception is thrown to stop validation
        }

        $this->errors = $manager->errors;

        return Utils::arrayIsEmpty($this->errors);
    }

    public function getErrors(bool $single): string
    {
        return $single ? array_shift($this->errors) : $this->errors;
    }

    public function setStopOnError(bool $value): void
    {
        $this->stopOnError = $value;
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    protected function getData($data)
    {
        if ($data instanceof BaseDocument) {
            return $data->getData();
        }

        return $this->loader->loadData($data);
    }
}
