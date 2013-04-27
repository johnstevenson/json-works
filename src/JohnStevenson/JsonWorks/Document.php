<?php

namespace JohnStevenson\JsonWorks;

class Document
{
    public $data;
    public $schema;
    public $lastError;
    public $lastPushIndex;
    private $element;
    private $workingData;
    private $validator;

    public function loadData($data, $noException = false)
    {
        $data = $this->getInput($data, false, $noException);
        $this->data = $data ? Utils::dataCopy($data) : null;
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
        $this->lastPushIndex = 0;
        $pointers = is_array($path) ? $path : Utils::pathDecode($path);
        $value = Utils::dataCopy($value);

        if (!$pointers) {
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
            $this->workingData = Utils::dataCopy($this->data);
        }

        # create any new keys and get referenced element
        if (!$this->workAdd($pointers, $arrayPush, $addKey)) {
            return false;
        }

        # finally add passed-in value to referenced element
        if ($arrayPush) {
            $this->lastPushIndex = array_push($this->element, $value) - 1;
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
        $pointers = is_array($path) ? $path : Utils::pathDecode($path);

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

        $pointers = is_array($path) ? $path : Utils::pathDecode($path);

        if ($this->workGet($pointers, false)) {
            $value = Utils::dataCopy($this->element);
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
        $this->data = Utils::dataPrune($this->data);
        if ($order && $this->schema) {
            $this->data = Utils::dataOrder($this->data, $this->schema->data);
        }
    }

    public function toJson($pretty, $tabs = false)
    {
        $json = Utils::dataToJson($this->data, $pretty);
        if ($tabs && $pretty) {
            $json = preg_replace_callback('/^( +)/m', function($m) {
                return str_repeat("\t", (int) strlen($m[1]) / 4);
            }, $json);
        }

        return $json;
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

        if ($this->lastError && !$noException) {
            throw new \RuntimeException($this->lastError);
        }

        return $this->lastError ? null : $input;
    }

    protected function getInputWork($input, $isSchema)
    {
        if (is_string($input)) {

            if (!preg_match('/^(\{|\[)/', $input)) {
                $input = @file_get_contents($input);
                if (false === $input) {
                    $this->lastError = 'Unable to open file: '.$input;
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
                return;
            }
        }

        while ($pointers) {

            $key = array_shift($pointers);

            if ($pointers) {

                if (is_array($this->element)) {

                    if (!$this->pushKey($key)) {
                        $this->lastError = 'Invalid array key';
                        return;
                    }

                    $this->element[0] = null;
                    $this->element = &$this->element[0];
                    if (!$this->workAddElement($pointers)) {
                        return;
                    }

                } else {

                    $this->element->$key = null;
                    $this->element = &$this->element->$key;

                    if (!$this->workAddElement($pointers)) {
                        return;
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
                        return;
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

        if (is_null($this->element)) {
            return false;
        }

        while ($pointers) {
            $type = gettype($this->element);
            $test = $pointers;
            $key = array_shift($test);
            $result = false;

            if ('object' === $type) {

                if ($result = property_exists($this->element, $key)) {
                    $this->element = &$this->element->$key;
                }

            } elseif ('array' === $type) {

                if ($result = $this->arrayKey($key, $index)) {
                    if ($result = array_key_exists($index, $this->element)) {
                        $this->element = &$this->element[$index];
                    }
                }
            }

            if (!$result) {
                return false;
            }

            array_shift($pointers);
         }

         return true;
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
