<?php

namespace JohnStevenson\JsonWorks\Schema;

use JohnStevenson\JsonWorks\Schema\Resolver;
use JohnStevenson\JsonWorks\Schema\ValidationException;
use JohnStevenson\JsonWorks\Schema\Constraints\Manager;

class Validator
{
    public $errors;

    public function check($data, $schema)
    {
        $resolver = new Resolver($schema);
        $manager = new Manager($resolver);

        try {
            $manager->validate($data, $schema);
        } catch (ValidationException $e) {
            // The exception is thrown to stop validation
        }

        $this->errors = $manager->errors;

        return empty($this->errors);
    }
}
