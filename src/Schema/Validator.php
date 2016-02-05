<?php

namespace JohnStevenson\JsonWorks\Schema;

use JohnStevenson\JsonWorks\Schema\ValidationException;

class Validator
{
    public $error;
    protected $errors;

    public function check($data, $model, $lax = false)
    {
        $result = true;
        $this->error = '';
        $constraints = new Constraints\Manager();

        try {
            $constraints->validate($data, $model->data);
        } catch (ValidationException $e) {
            $this->error = $e->getMessage();
            $result = false;
        }

        //return $result;
        return empty($this->error) && empty($constraints->errors);
    }
}
