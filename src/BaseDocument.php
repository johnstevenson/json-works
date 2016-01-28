<?php

namespace JohnStevenson\JsonWorks;

use JohnStevenson\JsonWorks\Helpers\Formatter;
use JohnStevenson\JsonWorks\Helpers\Loader;
use JohnStevenson\JsonWorks\Schema\Model;
use JohnStevenson\JsonWorks\Schema\Validator;

class BaseDocument
{
    public $data;
    public $schema;
    public $lastError;

    /**
    * @var Helpers\Formatter
    */
    protected $formatter;

    /**
    * @var Schema\Validator
    */
    protected $validator;

    public function __construct()
    {
        $this->formatter = new Formatter();
    }

    public function loadData($data)
    {
        $loader = new Loader();
        $this->data = $loader->load($data, false);
    }

    public function loadSchema($schema)
    {
        $loader = new Loader();
        $data = $loader->load($schema, true);
        $this->schema = new Model($data);
    }

    public function tidy($order = false)
    {
        $this->data = $this->formatter->prune($this->data);

        if ($order && $this->schema) {
            $this->data = $this->formatter->order($this->data, $this->schema->data);
        }
    }

    public function toJson($pretty)
    {
        $options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $options |= $pretty ? JSON_PRETTY_PRINT : 0;

        return $this->formatter->toJson($this->data, $options);
    }

    public function validate($lax = false)
    {
        if (!$this->schema) {
            return true;
        }

        if (!$this->validator) {
            $this->validator = new Validator();
        }

        if (!$result = $this->validator->check($this->data, $this->schema, $lax)) {
            $this->lastError = $this->validator->error;
        }

        return $result;
    }
}
