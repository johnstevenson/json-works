<?php declare(strict_types=1);

/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks;

use \stdClass;

use JohnStevenson\JsonWorks\Schema\Validator;

/**
 * A class for loading, formatting and validating json data. *
 * @api
 */
class BaseDocument
{
    /** @var mixed */
    protected $data;
    protected string $error = '';
    protected ?stdClass $schema = null;

    protected Formatter $formatter;
    protected ?Validator $validator = null;

    public function __construct()
    {
        $this->formatter = new Formatter();
    }

    /**
     * @param mixed $data
     */
    public function loadData($data): void
    {
        $loader = new Loader();
        $this->data = $loader->getData($data);
    }

    /**
     * @param mixed $data
     */
    public function loadSchema($data): void
    {
        $loader = new Loader();
        $this->schema = $loader->getSchema($data);
    }

    public function tidy(bool $order = false): void
    {
        $this->data = $this->formatter->prune($this->data);

        if ($order && $this->schema !== null) {
            $this->data = $this->formatter->order($this->data, $this->schema);
        }
    }

    /**
     * @return mixed $data
     */
    public function getData()
    {
        return $this->data;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function toJson(bool $pretty): string
    {
        $options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $options |= $pretty ? JSON_PRETTY_PRINT : 0;

        return $this->formatter->toJson($this->data, $options);
    }

    public function validate(): bool
    {
        if ($this->schema === null) {
            return true;
        }

        if ($this->validator === null) {
            $this->validator = new Validator($this->schema);
        }

        if (!$result = $this->validator->check($this->data)) {
            $this->error = $this->validator->getLastError();
        }

        return $result;
    }
}
