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

use JohnStevenson\JsonWorks\Helpers\Utils;
use JohnStevenson\JsonWorks\Schema\DataChecker;

class CommonConstraint extends BaseConstraint implements ConstraintInterface
{
    protected DataChecker $dataChecker;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->dataChecker = $this->manager->dataChecker;
    }

    /**
     * @param mixed $data
     * @param stdClass|array<mixed> $schema
     */
    public function validate($data, $schema, ?string $key = null): void
    {
        if (!($schema instanceof stdClass)) {
            $error = Utils::getArgumentError('$schema', 'sdtClass', $schema);
            throw new \InvalidArgumentException($error);
        }

        $this->run($data, $schema);
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
                $required = (array) $common[$key];
                $this->getValue($schema, $key, $subSchema, $required);
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

        if ($key !== 'not' && is_array($schema)) {
            $this->dataChecker->checkArray($schema, $key);
        }
    }

    /**
     * @param mixed $data
     * @param array<mixed> $subSchema
     */
    protected function check($data, $subSchema, string $key): void
    {
        switch ($key) {
            case 'enum':
                $class = EnumConstraint::class;
                break;
            case 'type':
                $class = TypeConstraint::class;
                break;
            default:
                $class = OfConstraint::class;
                break;
        }

        $validator =$this->manager->factory($class);
        $validator->validate($data, $subSchema, $key);

        /*
        if ($name === 'of') {
            $validator->validate($data, $subSchema, $key);
        } else {
            $validator->validate($data, $subSchema);
        }
        */
    }
}
