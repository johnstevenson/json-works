<?php declare(strict_types=1);

/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Schema;

use stdClass;

use JohnStevenson\JsonWorks\BaseDocument;
use JohnStevenson\JsonWorks\Loader;
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

    public function __construct(stdClass $schema)
    {
        $this->loader = new Loader;
        $this->schema = $this->loader->getSchema($schema);
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

    /**
     * @return array<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getLastError(): string
    {
        return $this->errors[0] ?? '';
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

        return $this->loader->getData($data);
    }
}
