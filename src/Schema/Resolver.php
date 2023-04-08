<?php declare(strict_types=1);

/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
