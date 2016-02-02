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

class ArrayConstraint extends BaseConstraint
{
    protected $comparer;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->comparer = new Comparer();
    }

    protected function run($data, $schema, $key = null)
    {
        // maxItems
        $this->checkMaxItems($data, $schema);

        // minItems
        $this->checkMinItems($data, $schema);

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

            $this->validateChild($data[$i], $subSchema, strval($i));
        }
    }

    protected function checkMaxItems($data, $schema)
    {
        if ($this->getInteger($schema, 'maxItems', $value)) {
            if (count($data) > $value) {
                $error = sprintf("has too many items, maximum '%d'", $value) ;
                $this->addError($error);
            }
        }
    }

    protected function checkMinItems($data, $schema)
    {
        if ($this->getInteger($schema, 'minItems', $value)) {
            if (count($data) < $value) {
                $error = sprintf("has too few items, minimum '%d'", $value) ;
                $this->addError($error);
            }
        }
    }

    protected function getInteger($schema, $key, &$value)
    {
        if ($result = $this->getValue($schema, $key, $value, $type)) {

            if ($type !== 'integer') {
                $this->throwSchemaError('integer', $value);
            }

            if ($value < 0) {
                $this->throwSchemaError('>= 0', $value);
            }
        }

        return $result;
    }
}
