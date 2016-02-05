<?php

namespace JohnStevenson\JsonWorks\Schema;

use JohnStevenson\JsonWorks\Schema\ValidationException;
use JohnStevenson\JsonWorks\Schema\Constraints\Manager;

class Validator
{
    public $errors;

    public function check($data, $model)
    {
        $manager = new Manager();

        try {
            $manager->validate($data, $model->data);
        } catch (ValidationException $e) {
        }

        $this->errors = $manager->errors;

        return empty($this->errors);
    }
}
