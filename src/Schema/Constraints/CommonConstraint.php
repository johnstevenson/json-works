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
    protected $dataChecker;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->dataChecker = $this->manager->dataChecker;
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
                $this->getValue($schema, $key, $subSchema, $common[$key]);
                $this->checkSchema($subSchema, $key);

                $this->check($data, $subSchema, $key);
            }
        }
    }

    protected function checkSchema(&$schema, $key)
    {
        if ($key === 'type') {
            $schema = (array) $schema;
        }

        if ($key !== 'not') {
            $this->dataChecker->checkArray($schema, $key);
        }
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
}
