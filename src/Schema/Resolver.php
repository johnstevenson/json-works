<?php

namespace JohnStevenson\JsonWorks\Schema;

use JohnStevenson\JsonWorks\Helpers\Loader;

class Resolver
{
    /**
    * @var \JohnStevenson\JsonWorks\Helpers\Loader
    */
    protected $loader;

    /**
    * @var cache
    */
    protected $cache;

    protected $basePath;

    public function __construct($schema, $basePath)
    {
        $this->loader = new Loader;
        $this->cache = new Cache($schema);
        $this->basePath = $basePath;
    }

    public function getRef($ref)
    {
        return $this->cache->resolveRef($ref);
    }
}
