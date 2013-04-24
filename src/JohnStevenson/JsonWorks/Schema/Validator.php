<?php

namespace JohnStevenson\JsonWorks\Schema;

class Validator
{
    public $error;

    public function check($data, $model, $lax = false)
    {
        $result = true;
        $this->error = '';
        $constraints = new Constraints($lax);

        try {
            $constraints->validate($data, $model->data);
        } catch (ValidationException $e) {
            $this->error = $e->getMessage();
            $result = false;
        }

        return $result;
    }
}
