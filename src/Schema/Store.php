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

class Store
{
    /** @var array<string, array<string, stdClass>> */
    protected array $data = [];

    public function add(string $doc, string $path, stdClass $schema): bool
    {
        if (!$this->matchPath($doc, $path, $partPath)) {
            $this->data[$doc][$path] = $schema;
            return true;
        }

        return $path !== $partPath;
    }

    /**
     * Adds the schema to the root and returns false if it already existed
     */
    public function addRoot(string $doc, stdClass $schema): bool
    {
        $found = $this->hasRoot($doc);

        if (!$found) {
            $this->data[$doc] = ['#' => $schema];
        }

        return !$found;
    }

    public function hasRoot(string $doc): bool
    {
        return isset($this->data[$doc]);
    }

    /**
     * Returns a schema if found, otherwise sets $data
     *
     * @param string $path
     * @param mixed $data Set by method
     */
    public function get(string $doc, string &$path, &$data): ?stdClass
    {
        if (isset($this->data[$doc][$path])) {
            return $this->data[$doc][$path];
        }

        $data = isset($this->data[$doc]) ? $this->getData($doc, $path) : null;

        return null;
    }

    /**
     * @return mixed
     */
    protected function getData(string $doc, string &$path)
    {
        if (!$this->matchPath($doc, $path, $partPath)) {
            return $this->data[$doc]['#'];
        } else {
            $path = substr($path, strlen($partPath));
            return $this->data[$doc][$partPath];
        }
    }

    protected function matchPath(string $doc, string $path, ?string &$partPath): bool
    {
        foreach ($this->data[$doc] as $key => $dummy) {
            if (0 === strpos($path.'/', $key.'/')) {
                $partPath = $key;
                return true;
            }
        }

        return false;
    }
}
