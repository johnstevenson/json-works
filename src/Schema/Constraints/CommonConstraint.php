<?php
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Schema\Constraints;

use JohnStevenson\JsonWorks\Schema\Constraints\Manager;

class CommonConstraint extends BaseConstraint
{
    protected $comparer;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->comparer = new Comparer();
    }

    public function validate($data, $schema)
    {
        $errors = count($this->manager->errors);
        $this->run($data, $schema);

        return count($this->manager->errors) === $errors;
    }

    protected function run($data, $schema)
    {
        $common = [
            'enum' => 'array',
            'type' => ['array', 'string'],
            'allOf' => 'array',
            'anyOf' => 'array',
            'oneOf' => 'array',
            'not' => 'object'
        ];

        foreach ($schema as $key => $subSchema) {

            if (isset($common[$key])) {
                $this->getValue($schema, $key, $subSchema, $type, $common[$key]);
                $this->checkSchema($subSchema, $key, $common[$key]);

                $this->check($data, $subSchema, $key);
            }
        }
    }

    protected function checkSchema($schema, $key, $required)
    {
        if ($key === 'type') {
            $schema = (array) $schema;
        }

        if (!is_array($schema)) {
            return;
        }

        $this->checkArrayCount($schema);
        $this->checkArrayValues($schema, $key);
    }

    protected function check($data, $subSchema, $key)
    {
        $name = in_array($key, ['enum', 'type']) ? $key : 'of';
        $validator = $this->manager->factory($name);

        if ($name === 'of') {
            $validator->validate($data, $subSchema, $key);
        } else {
            $validator->validate($data, $subSchema);
        }
    }

    protected function checkArrayCount(array $schema)
    {
        if (count($schema) <= 0) {
            $error = $this->getSchemaError('> 0', '0');
            throw new \RuntimeException($error);
        }
    }

    protected function checkArrayValues(array $schema, $key)
    {
        switch ($key) {
            case 'enum':
                break;
            case 'type':
                $this->checkTypes($schema);
                break;
            default:
                $this->checkArrayTypes($schema, 'object');
        }
    }

    protected function checkTypes(array $schema)
    {
        $this->checkArrayTypes($schema, 'string');

        $types = ['array', 'boolean', 'integer', 'null', 'number', 'object', 'string'];

        if ($unknown = array_diff($schema, $types)) {
            $error = $this->getSchemaError(implode('|', $types), implode('', $unknown));
            throw new \RuntimeException($error);
        }

        if (!$this->comparer->uniqueArray($schema)) {
            $error = $this->getSchemaError('unique', 'duplicates');
            throw new \RuntimeException($error);
        }
    }

    protected function checkArrayTypes(array $schema, $type)
    {
        if (!$this->comparer->arrayOfType($schema, $type)) {
            $error = $this->getSchemaError($type, 'mixed');
            throw new \RuntimeException($error);
        }
    }
}
