<?php

namespace JohnStevenson\JsonWorks;

use JohnStevenson\JsonWorks\Helpers\Builder;
use JohnStevenson\JsonWorks\Helpers\Finder;
use JohnStevenson\JsonWorks\Helpers\Formatter;
use JohnStevenson\JsonWorks\Helpers\Tokenizer;

class Document
{
    public $data;
    public $schema;
    public $lastError;

    /**
    * @var Builder
    */
    protected $builder;

    /**
    * @var Finder
    */
    protected $finder;

    /**
    * @var Formatter
    */
    protected $formatter;

    /**
    * @var Tokenizer
    */
    protected $tokenizer;

    private $element;
    private $workingData;
    private $validator;

    public function __construct()
    {
        $this->builder = new Builder();
        $this->finder = new Finder();
        $this->formatter = new Formatter();
        $this->tokenizer = new Tokenizer();
    }

    public function loadData($data, $noException = false)
    {
        $data = $this->getInput($data, false, $noException);
        $this->data = $data ? $this->formatter->copy($data) : null;
        $this->workingData = null;
        return empty($this->lastError);
    }

    public function loadSchema($schema, $noException = false)
    {
        $this->schema = $this->getInput($schema, true, $noException);
        return empty($this->lastError);
    }

    public function addValue($path, $value)
    {
        $this->lastError = null;
        $value = $this->formatter->copy($value);

        if ($result = $this->builder->add($this->data, $path, $value)) {
            $this->data = $this->builder->getData();
        } else {
            $this->lastError = $this->builder->getError();
        }

        return $result;
    }

    public function copyValue($fromPath, $toPath)
    {
        return $this->workMove($fromPath, $toPath, false);
    }

    public function deleteValue($path)
    {
        if ($result = $this->hasValue($path, $dummy)) {

            $pointers = $this->tokenizer->decode($path);
            $key = array_pop($pointers);
            $path = $this->tokenizer->encode($pointers);

            $this->hasValue($path, $dummy);

            if (0 === strlen($key)) {
                $this->loadData(null);
            } elseif (is_array($this->element)) {
                $key = (int) $key;
                array_splice($this->element, $key, 1);
            } elseif (is_object($this->element)) {
                unset($this->element->$key);
            }
        }

        return $result;
    }

    public function getValue($path, $default = null)
    {
        if (!$this->hasValue($path, $value)) {
            $value = $default;
        }

        return $value;
    }

    public function hasValue($path, &$value)
    {
        $result = false;
        $value = null;

        $pointers = $this->tokenizer->decode($path);

        if ($this->workGet($pointers, false)) {
            $value = $this->formatter->copy($this->element);
            $result = true;
        }

        return $result;
    }

    public function moveValue($fromPath, $toPath)
    {
        return $this->workMove($fromPath, $toPath, true);
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

        return json_encode($this->data, $options);
    }

    public function validate($lax = false)
    {
        return $this->checkData($this->data, $lax);
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
                $input = new Schema\Model($input);
            } catch (\RuntimeException $e) {
                $result = false;
                $this->lastError = 'Schema error: '.$e->getMessage();
            }
        }

        return $result ? $input : false;
    }

    protected function workGet(&$pointers, $forEdit)
    {
        if ($forEdit) {
            $this->element = &$this->workingData;
        } else {
            $this->element = &$this->data;
        }

        $this->element =& $this->finder->get($this->element, $pointers, $found);

        return $found;
    }

    protected function workMove($fromPath, $toPath, $delete)
    {
        $result = false;

        if ($this->hasValue($fromPath, $value)) {
            if ($result = $this->addValue($toPath, $value)) {
                if ($delete) {
                    $this->deleteValue($fromPath);
                }
            }
        }

        return $result;
    }

    protected function checkData($data, $lax = false)
    {
        if (!$this->schema) {
            return true;
        }

        if (!$this->validator) {
            $this->validator = new Schema\Validator();
        }

        if (!$result = $this->validator->check($data, $this->schema, $lax)) {
            $this->lastError = $this->validator->error;
        }

        return $result;
    }
}
