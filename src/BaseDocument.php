<?php
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks;

use JohnStevenson\JsonWorks\Helpers\Formatter;
use JohnStevenson\JsonWorks\Helpers\Loader;
use JohnStevenson\JsonWorks\Schema\Validator;

/**
* A class for loading, validating json data
*/
class BaseDocument
{
    public $data;
    public $lastError;
    protected $schema;
    protected $schemaPath = '';

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
        $this->data = $loader->loadData($data);
    }

    public function loadSchema($data, $basePath = '')
    {
        $loader = new Loader();
        $this->schema = $loader->loadSchema($data, $filename);
        $this->schemaPath = $basePath;
    }

    public function tidy($order = false)
    {
        $this->data = $this->formatter->prune($this->data);

        if ($order && $this->schema) {
            $this->data = $this->formatter->order($this->data, $this->schema);
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function toJson($pretty)
    {
        $options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $options |= $pretty ? JSON_PRETTY_PRINT : 0;

        return $this->formatter->toJson($this->data, $options);
    }

    public function validate()
    {
        if (!$this->schema) {
            return true;
        }

        if (!$this->validator) {
            $this->validator = new Validator($this->schema);
        }

        if (!$result = $this->validator->check($this->data, $this->schema)) {
            $this->lastError = $this->validator->getErrors(1);
        }

        return $result;
    }
}
