<?php declare(strict_types=1);

namespace JohnStevenson\JsonWorks\Schema;

use \stdClass;

class Resolver
{
    protected Cache $cache;

    public function __construct(stdClass $schema)
    {
        $this->cache = new Cache($schema);
    }

    public function getRef(string $ref): stdClass
    {
        return $this->cache->resolveRef($ref);
    }
}
