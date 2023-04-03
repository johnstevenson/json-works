<?php declare(strict_types=1);

/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Schema\Constraint;

use \stdClass;

use JohnStevenson\JsonWorks\Schema\DataChecker;

class CommonConstraint extends BaseConstraint
{
    protected DataChecker $dataChecker;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->dataChecker = $this->manager->dataChecker;
    }

    /**
     * @param mixed $data
     */
    public function validate($data, stdClass $schema): bool
    {
        $errors = count($this->manager->errors);
        $this->run($data, $schema);

        return count($this->manager->errors) === $errors;
    }

    /**
     * @param mixed $data
     */
    protected function run($data, stdClass $schema): void
    {
        $common = [
            'enum' => 'array',
            'type' => ['array', 'string'],
            'allOf' => 'array',
            'anyOf' => 'array',
            'oneOf' => 'array',
            'not' => 'object'
        ];

        foreach (get_object_vars($schema) as $key => $subSchema) {
            if (isset($common[$key])) {
                $this->getValue($schema, $key, $subSchema, $common[$key]);
                $this->checkSchema($subSchema, $key);
                $this->check($data, $subSchema, $key);
            }
        }
    }

    /**
     * @param mixed $schema
     */
    protected function checkSchema(&$schema, string $key): void
    {
        if ($key === 'type') {
            $schema = (array) $schema;
        }

        if ($key !== 'not') {
            $this->dataChecker->checkArray($schema, $key);
        }
    }

    /**
     * @param mixed $data
     * @param mixed $subSchema
     */
    protected function check($data, $subSchema, string $key): void
    {
        $name = in_array($key, ['enum', 'type'], true) ? $key : 'of';
        $validator = $this->manager->factory($name);

        if ($name === 'of') {
            $validator->validate($data, $subSchema, $key);
        } else {
            $validator->validate($data, $subSchema);
        }
    }
}
