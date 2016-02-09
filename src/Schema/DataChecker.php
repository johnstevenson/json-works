<?php
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Schema;

use JohnStevenson\JsonWorks\Schema\Comparer;

class DataChecker
{
    protected $comparer;

    public function __construct()
    {
        $this->comparer = new Comparer();
    }

    public function checkType($value, $required)
    {
        $type = $this->comparer->getSpecific($value);

        if ($required !== null) {

            $types = (array) $required;

            if (!in_array($type, $types)) {
                $error = $this->formatError(implode('|', $types), $type);
                throw new \RuntimeException($error);
            }
        }
    }

    public function checkArray(array $schema, $key)
    {
        if ($key !== 'type') {
            $this->checkArrayCount($schema);
        }

        $this->checkArrayValues($schema, $key);
    }

    public function checkContainerTypes($schema, $type)
    {
        foreach ($schema as $value) {
            if (!$this->comparer->checkType($value, $type)) {
                $error = $this->formatError($type, 'mixed');
                throw new \RuntimeException($error);
            }
        }
    }

    public function emptySchema($schema)
    {
        $this->checkType($schema, 'object');

        return count((array) $schema) === 0;
    }

    public function checkForRef($schema, &$ref)
    {
        if ($result = (is_object($schema) && property_exists($schema, '$ref'))) {
            $ref = $schema->{'$ref'};
            $this->checkType($ref, 'string');
        }

        return $result;
    }

    public function formatError($expected, $value)
    {
        return sprintf(
            "Invalid schema value: expected '%s', got '%s'",
            $expected,
            $value
        );
    }

    protected function checkArrayCount(array $schema)
    {
        if (count($schema) <= 0) {
            $error = $this->formatError('> 0', '0');
            throw new \RuntimeException($error);
        }
    }

    protected function checkArrayValues(array $schema, $key)
    {
        if (in_array($key, ['enum', 'type', 'required'])) {
            $this->checkUnique($schema);
        }

        if ($key !== 'enum') {
            $type = in_array($key, ['type', 'required']) ? 'string' : 'object';
            $this->checkContainerTypes($schema, $type);
        }
    }

    protected function checkUnique(array $schema)
    {
        if (!$this->comparer->uniqueArray($schema)) {
            $error = $this->formatError('unique', 'duplicates');
            throw new \RuntimeException($error);
        }
    }
}
