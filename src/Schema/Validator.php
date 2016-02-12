<?php

namespace JohnStevenson\JsonWorks\Schema;

use JohnStevenson\JsonWorks\BaseDocument;
use JohnStevenson\JsonWorks\Helpers\Loader;
use JohnStevenson\JsonWorks\Schema\Resolver;
use JohnStevenson\JsonWorks\Schema\ValidationException;
use JohnStevenson\JsonWorks\Schema\Constraints\Manager;

class Validator
{
    protected $errors = [];
    protected $schema;
    protected $stopOnError = false;

    /**
    * @var \JohnStevenson\JsonWorks\Helpers\Loader
    */
    protected $loader;

    /**
    * @var Resolver
    */
    protected $resolver;

    public function __construct($schema, $basePath = '')
    {
        $this->loader = new Loader;
        $this->loadSchema($schema, $basePath);
        $this->resolver = new Resolver($this->loader, $this->schema, $basePath);
    }

    public function check($data, $schema)
    {
        $data = $this->getData($data);
        $manager = new Manager($this->resolver, $this->stopOnError);

        try {

            $manager->validate($data, $this->schema);
        } catch (ValidationException $e) {
            // The exception is thrown to stop validation
        }

        $this->errors = $manager->errors;

        return empty($this->errors);
    }

    public function getErrors($single)
    {
        return $single ? array_shift($this->errors) : $this->errors;
    }

    public function setStopOnError($value)
    {
        $this->stopOnError = $value;
    }

    protected function getData($data)
    {
        if ($data instanceof BaseDocument) {
            return $data->getData();
        }

        return $this->loader->loadData($data);
    }

    protected function loadSchema($data, &$basePath)
    {
        $this->schema = $this->loader->loadSchema($data, $file);

        if ($file && !$basePath) {
            $basePath = $this->getRealpath($data, true);
        } elseif ($basePath) {
            $basePath = $this->getRealpath($data, false);
        }
    }

    protected function getRealpath($path, $dirname)
    {
        if (!$realpath = realpath($path)) {
            return '';
        }

        if ($dirname) {
            $realpath = dirname($realpath);
            $realpath = $realpath !== '.' ? $realpath : '';
        }

        return str_replace('\\', '/', $realpath) . '/';
    }
}
