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
use JohnStevenson\JsonWorks\Schema\Constraints\Manager;

class DataChecker
{
    protected $comparer;
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        $this->comparer = new Comparer();
    }

    public function checkType($value, $required)
    {
        $type = $this->comparer->getSpecific($value);

        if ($required !== null) {

            $types = (array) $required;

            if (!in_array($type, $types)) {
                $error = $this->manager->getSchemaError(implode('|', $types), $type);
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

    public function checkObject($schema, $type)
    {
        foreach ($schema as $key => $value) {
            if (!$this->comparer->checkType($value, $type)) {
                $error = $this->manager->getSchemaError($type, 'mixed');
                throw new \RuntimeException($error);
            }
        }
    }

    public function emptySchema($schema)
    {
        $this->checkType($schema, 'object');

        return count((array) $schema) === 0;
    }

    protected function checkArrayCount(array $schema)
    {
        if (count($schema) <= 0) {
            $error = $this->manager->getSchemaError('> 0', '0');
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
            $this->checkArrayTypes($schema, $type);
        }
    }

    protected function checkUnique(array $schema)
    {
        if (!$this->comparer->uniqueArray($schema)) {
            $error = $this->manager->getSchemaError('unique', 'duplicates');
            throw new \RuntimeException($error);
        }
    }

    protected function checkArrayTypes(array $schema, $type)
    {
        if (!$this->comparer->arrayOfType($schema, $type)) {
            $error = $this->manager->getSchemaError($type, 'mixed');
            throw new \RuntimeException($error);
        }
    }
}
