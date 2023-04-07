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
use JohnStevenson\JsonWorks\Schema\JsonTypes;
use JohnStevenson\JsonWorks\Schema\Constraint\Manager;

class SpecificConstraint extends BaseConstraint implements ConstraintInterface
{
    protected JsonTypes $jsonTypes;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->jsonTypes = new JsonTypes();
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

        $dataType = $this->getInstanceType($data);
        $class = null;

        if (Utils::stringIsEmpty($dataType)) {
            return;
        }

        switch ($dataType) {
            case 'array':
                $class = ArrayConstraint::class;
                break;
            case 'number':
                $class = NumberConstraint::class;
                break;
            case 'object':
                $class = ObjectConstraint::class;
                break;
            case 'string':
                $class = StringConstraint::class;
                break;
        }

        if ($class === null) {
            throw new \RuntimeException('Unknown constraint: '.$dataType);
        }

        $validator = $this->manager->factory($class);
        $validator->validate($data, $schema);
    }

    /**
    * @param mixed $data
    */
    protected function getInstanceType($data): string
    {
        $result = $this->jsonTypes->getGeneric($data);

        if (in_array($result, ['boolean', 'null'], true)) {
            $result = '';
        }

        return $result;
    }
}
