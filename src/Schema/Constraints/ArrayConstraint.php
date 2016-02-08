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

use JohnStevenson\JsonWorks\Schema\Comparer;
use JohnStevenson\JsonWorks\Schema\Constraints\ItemsConstraint;
use JohnStevenson\JsonWorks\Schema\Constraints\Manager;
use JohnStevenson\JsonWorks\Schema\Constraints\MaxMinConstraint;

class ArrayConstraint extends BaseConstraint
{
    protected $comparer;
    protected $items;
    protected $maxMin;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->comparer = new Comparer();
        $this->items = new ItemsConstraint($manager);
        $this->maxMin = new MaxMinConstraint($manager);
    }

    public function validate($data, $schema)
    {
        // max and min
        $this->checkMaxMin($data, $schema);

        // uniqueItems
        $this->checkUnique($data, $schema);

        $this->items->validate($data, $schema);
    }

    protected function checkMaxMin($data, $schema)
    {
        // maxItems
        $this->maxMin->validate($data, $schema, 'maxItems');

        // minItems
        $this->maxMin->validate($data, $schema, 'minItems');
    }

    protected function checkUnique($data, $schema)
    {
        if ($this->isUnique($schema)) {

            if (!$this->comparer->uniqueArray($data)) {
                $this->addError('contains duplicate values');
            }
        }
    }

    protected function isUnique($schema)
    {
        if ($this->getValue($schema, 'uniqueItems', $value, 'boolean')) {
            return $value;
        }

        return false;
    }
}
