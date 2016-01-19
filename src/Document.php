<?php

namespace JohnStevenson\JsonWorks;

use JohnStevenson\JsonWorks\Helpers\Finder;
use JohnStevenson\JsonWorks\Helpers\Formatter;
use JohnStevenson\JsonWorks\Helpers\Tokenizer;

class Document
{
    public $data;
    public $schema;
    public $lastError;

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
        $pointers = is_array($path) ? $path : $this->tokenizer->decode($path);
        $value = $this->formatter->copy($value);

        if (empty($pointers)) {
            # empty path, add value to root
            if ($result = (is_object($value) || is_array($value)) && $this->checkData($value, true)) {
                $this->data = $value;
            } else {
                $this->lastError = $this->lastError ?: 'Value must be an object or array';
            }

            return $result;
        }

        if (is_null($this->data)) {
            # data not initiated
            $this->workingData = null;
        } else {
            # data exists so copy it
            $this->workingData = $this->formatter->copy($this->data);
        }

        # create any new keys and get referenced element
        if (!$this->workAdd($pointers, $arrayPush, $addKey)) {
            return false;
        }

        # finally add passed-in value to referenced element
        if ($arrayPush) {
            array_push($this->element, $value);
        } elseif (null !== $addKey) {
            $this->element->$addKey = $value;
        } else {
            $this->element = $value;
        }

        if ($result = $this->checkData($this->workingData, true)) {
            $this->data = $this->workingData;
        }

        return $result;
    }

    public function copyValue($fromPath, $toPath)
    {
        return $this->workMove($fromPath, $toPath, false);
    }

    public function deleteValue($path)
    {
        $pointers = is_array($path) ? $path : $this->tokenizer->decode($path);

        if ($result = $this->hasValue($pointers, $dummy)) {

            $key = array_pop($pointers);
            $this->hasValue($pointers, $dummy);

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

        $pointers = is_array($path) ? $path : $this->tokenizer->decode($path);

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

    protected function pushKey($value)
    {
        return (bool) preg_match('/^((-)|(0+))$/', $value);
    }

    protected function arrayKey($value, &$index, $any = false)
    {
        $index = null;

        if ($any && '-' === $value) {
            $index = '-';
        } elseif (preg_match('/^0*\d+$/', $value)) {
            $index = (int) $value;
        }

        return null !== $index;
    }

    protected function workAdd($pointers, &$arrayPush, &$addKey)
    {
        $this->workGet($pointers, true);
        $arrayPush = false;
        $addKey  = null;

        if (is_null($this->element)) {
            if (!$this->workAddElement($pointers)) {
                return false;
            }
        }

        while (!empty($pointers)) {

            $key = array_shift($pointers);

            if (!empty($pointers)) {

                if (is_array($this->element)) {

                    if (!$this->pushKey($key)) {
                        $this->lastError = 'Invalid array key';
                        return false;
                    }

                    $this->element[0] = null;
                    $this->element = &$this->element[0];
                    if (!$this->workAddElement($pointers)) {
                        return false;
                    }

                } else {

                    $this->element->$key = null;
                    $this->element = &$this->element->$key;

                    if (!$this->workAddElement($pointers)) {
                        return false;
                    }
                }

            } else {
                 # no more pointers. First check for array with final array key

                if (is_array($this->element)) {

                    if ($this->arrayKey($key, $index, true)) {
                        $index = is_int($index) ? $index : count($this->element);
                        $arrayPush = $index === count($this->element);
                    }

                    if (!$arrayPush) {
                        $this->lastError = 'Bad array index';
                        return false;
                    }

                } else {
                    $addKey = $key;
                }
            }
        }

        return true;
    }

    protected function workAddElement($pointers)
    {
        $arrayFirst = $this->pushKey($pointers[0]);
        $this->element = $arrayFirst ? array() : new \stdClass();

        if (!$result = $this->checkData($this->workingData, true)) {
            $this->element = !$arrayFirst ? array() : new \stdClass();
            $result = $this->checkData($this->workingData, true);
        }

        return $result;
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
