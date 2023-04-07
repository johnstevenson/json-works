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

use JohnStevenson\JsonWorks\Schema\Comparer;
use JohnStevenson\JsonWorks\Schema\Constraint\ItemsConstraint;
use JohnStevenson\JsonWorks\Schema\Constraint\Manager;
use JohnStevenson\JsonWorks\Schema\Constraint\MaxMinConstraint;
use JohnStevenson\JsonWorks\Helpers\Utils;

class ArrayConstraint extends BaseConstraint implements ConstraintInterface
{
    protected Comparer $comparer;
    protected ItemsConstraint $items;
    protected MaxMinConstraint $maxMin;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->comparer = new Comparer();
        $this->items = new ItemsConstraint($manager);
        $this->maxMin = new MaxMinConstraint($manager);
    }

    /**
     * @param mixed $data
     * @param stdClass|array<mixed> $schema
     */
    public function validate($data, $schema, ?string $key = null): void
    {
        if (!is_array($data)) {
            $error = Utils::getArgumentError('$data', 'array', $data);
            throw new \InvalidArgumentException($error);
        }

        if (!($schema instanceof stdClass)) {
            $error = Utils::getArgumentError('$schema', 'sdtClass', $schema);
            throw new \InvalidArgumentException($error);
        }

        // max and min
        $this->checkMaxMin($data, $schema);

        // uniqueItems
        $this->checkUnique($data, $schema);

        $this->items->validate($data, $schema);
    }

    /**
     * @param array<mixed> $data
     */
    protected function checkMaxMin(array $data, stdClass $schema): void
    {
        // maxItems
        $this->maxMin->validate($data, $schema, 'maxItems');

        // minItems
        $this->maxMin->validate($data, $schema, 'minItems');
    }

    /**
     * @param array<mixed> $data
     */
    protected function checkUnique(array $data, stdClass $schema): void
    {
        if ($this->isUnique($schema)) {

            if (!$this->comparer->uniqueArray($data)) {
                $this->addError('contains duplicate values');
            }
        }
    }

    protected function isUnique(stdClass $schema): bool
    {
        if ($this->getValue($schema, 'uniqueItems', $value, ['boolean'])) {
            return $value;
        }

        return false;
    }
}
