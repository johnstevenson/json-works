<?php

namespace JohnStevenson\JsonWorks\Schema;

use \JohnStevenson\JsonWorks\Utils as Utils;

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
        } catch (\RuntimeException $e) {
            $this->error = $e->getMessage();
            $result = false;
        }

        return $result;
    }
}
