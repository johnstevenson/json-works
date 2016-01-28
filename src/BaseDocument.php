<?php

namespace JohnStevenson\JsonWorks;

use JohnStevenson\JsonWorks\Helpers\Formatter;
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

    public function loadData($data, $noException = false)
    {
        $data = $this->getInput($data, false, $noException);
        $this->data = $data ? $this->formatter->copy($data) : null;
        return empty($this->lastError);
    }

    public function loadSchema($schema, $noException = false)
    {
        $this->schema = $this->getInput($schema, true, $noException);
        return empty($this->lastError);
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

    protected function getInput($input, $isSchema, $noException)
    {
        $this->lastError = null;
        $input = $this->getInputWork($input, $isSchema);

        if (false === $input) {
            $this->lastError = $this->lastError ?: 'Invalid input';
        }

        if ($this->lastError !== null && !$noException) {
            throw new \RuntimeException($this->lastError);
        }

        return $this->lastError ? null : $input;
    }

    protected function getInputWork($input, $isSchema)
    {
        if (is_string($input)) {

            if (!preg_match('/^(\{|\[)/', $input)) {
                $filename = $input;

                if (!$input = @file_get_contents($filename)) {
                    if (false === $input) {
                        $this->lastError = 'Unable to open file: '.$filename;
                    } else {
                        $this->lastError = 'File is empty: '.$filename;
                    }

                    return false;
                }
            }

            $input = json_decode($input);
            if (json_last_error()) {
                return false;
            }
        }

        if (is_array($input) || is_null($input)) {
            $result = !$isSchema;
        } else {
            $result = is_object($input);
        }

        if ($result && $isSchema) {
            try {
                $input = new Model($input);
            } catch (\RuntimeException $e) {
                $result = false;
                $this->lastError = 'Schema error: '.$e->getMessage();
            }
        }

        return $result ? $input : false;
    }
}
