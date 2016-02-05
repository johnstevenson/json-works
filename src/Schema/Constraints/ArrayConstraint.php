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

use JohnStevenson\JsonWorks\Schema\Constraints\Comparer;
use JohnStevenson\JsonWorks\Schema\Constraints\Manager;
use JohnStevenson\JsonWorks\Schema\Constraints\MaxMinConstraint;

class ArrayConstraint extends BaseConstraint
{
    protected $comparer;
    protected $maxMin;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->comparer = new Comparer();
        $this->maxMin = new MaxMinConstraint($manager);
    }

    public function validate($data, $schema)
    {
        // maxItems
        $this->maxMin->validate($data, $schema, 'maxItems');

        // minItems
        $this->maxMin->validate($data, $schema, 'minItems');

        // uniqueItems
        if ($this->get($schema, 'uniqueItems', false)) {
            if (!$this->comparer->uniqueArray($data)) {
                $this->addError('contains duplicate values');
            }
        }

        # items
        $items = $this->get($schema, 'items', array());

        # additionalItems
        $additional = $this->get($schema, 'additionalItems', true);

        if (false === $additional && is_array($items)) {
            if (count($data) > count($items)) {
                $this->addError('contains more elements than are allowed');
            }
        }

        $this->validateChildren($data, $items, $additional);
    }

    protected function validateChildren($data, $items, $additional)
    {
        if (null === $items) {
            $items = new \stdClass();
        }

        if (true === $additional) {
            $additional = new \stdClass();
        }

        $single = is_object($items);
        $dataCount = count($data);
        $itemsCount = !$single ? count($items) : 0;

        for ($i = 0; $i < $dataCount; ++$i) {

            if ($single) {
                $subSchema = $items;
            } elseif ($i < $itemsCount) {
                $subSchema = $items[$i];
            } elseif ($additional) {
                $subSchema = $additional;
            } else {
                continue;
            }

            $this->manager->validate($data[$i], $subSchema, strval($i));
        }
    }
}
