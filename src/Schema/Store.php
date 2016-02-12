<?php

namespace JohnStevenson\JsonWorks\Schema;

class Store
{
    /**
    * @var array
    */
    protected $data = [];

    public function add($doc, $path, $schema)
    {
        if (!$this->matchPath($doc, $path, $partPath)) {
            $this->data[$doc][$path] = $schema;
            return true;
        }

        return $path !== $partPath;
    }

    public function addRoot($doc, $schema)
    {
        if (!$found = $this->hasRoot($doc)) {
            $this->data[$doc] = ['#' => $schema];
        }

        return !$found;
    }

    public function hasRoot($doc)
    {
        return isset($this->data[$doc]);
    }

    public function get($doc, &$path, &$data)
    {
        if (isset($this->data[$doc][$path])) {
            return $this->data[$doc][$path];
        }

        $data = isset($this->data[$doc]) ? $this->getData($doc, $path) : null;
    }

    protected function getData($doc, &$path)
    {
        if (!$this->matchPath($doc, $path, $partPath)) {
            return $this->data[$doc]['#'];
        } else {
            $path = substr($path, strlen($partPath));
            return $this->data[$doc][$partPath];
        }
    }

    protected function matchPath($doc, $path, &$partPath)
    {
        foreach ($this->data[$doc] as $key => $dummy) {
            if (0 === strpos($path, $key)) {
                $partPath = $key;
                return true;
            }
        }

        return false;
    }
}
