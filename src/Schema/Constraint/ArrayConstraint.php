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

class ArrayConstraint extends BaseConstraint
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
     * @param array<mixed> $data
     */
    public function validate(array $data, stdClass $schema): void
    {
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
        if ($this->getValue($schema, 'uniqueItems', $value, 'boolean')) {
            return $value;
        }

        return false;
    }
}
