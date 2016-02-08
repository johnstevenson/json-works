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

class ItemsConstraint extends BaseConstraint
{
    /**
    * The main method
    *
    * @param mixed $data
    * @param mixed $schema
    */
    public function validate($data, $schema)
    {
        $this->getItemValue($schema, 'items', $items);
        $this->getItemValue($schema, 'additionalItems', $additional);

        if (is_object($items)) {
            $this->validateObjectItems($data, $items);
        } else {
            $this->checkArrayItems($data, $items, $additional);
            $this->validateArrayItems($data, $items, $additional);
        }
    }

    protected function getItemValue($schema, $key, &$value)
    {
        $value = null;
        $items = $key === 'items';

        $required = ['object'];
        $required[] = $items ? 'array' : 'boolean';

        if (!$this->getValue($schema, $key, $value, $required)) {
            if ($items) {
                $value = [];
            }
        }
    }

    protected function validateObjectItems($data, $schema)
    {
        $key = 0;

        foreach ($data as $value) {
            $this->manager->validate($value, $schema, strval($key));
            ++$key;
        }
    }

    protected function checkArrayItems($data, $items, $additional)
    {
        if (false === $additional) {
            if (count($data) > count($items)) {
                $this->addError('contains more elements than are allowed');
            }
        }
    }

    protected function validateArrayItems($data, $items, $additional)
    {
        $key = 0;
        $itemsCount = count($items);

        foreach ($data as $value) {

            if ($key < $itemsCount) {
                $this->manager->validate($value, $items[$key], strval($key));
            } else {

                if (is_object($additional)) {
                    $this->validateObjectItems(array_slice($data, $key), $additional);
                }
                break;
            }

            ++$key;
        }
    }
}
