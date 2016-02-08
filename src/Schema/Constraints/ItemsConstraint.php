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
    * @var object|bool
    */
    protected $additionalItems;

    /**
    * @var array
    */
    protected $data;

    /**
    * @ var object|array
    */
    protected $items;

    /**
    * @var integer
    */
    protected $dataCount;

    /**
    * @var integer
    */
    protected $itemsCount;

    /**
    * The main method
    *
    * @param mixed $data
    * @param mixed $schema
    */
    public function validate($data, $schema)
    {
        $this->setValues($data, $schema);

        if (is_object($this->items)) {
            $this->validateObjectItems($this->items);
        } else {
            $this->checkArrayItems();
            $this->validateArrayItems();
        }
    }

    protected function setValues($data, $schema)
    {
        $this->data = $data;
        $this->dataCount = count($this->data);

        $this->getItemValues($schema);
        $this->itemsCount = is_array($this->items) ? count($this->items) : 0;
    }

    protected function getItemValues($schema)
    {
        $values = [
            'additionalItems' => ['boolean', 'object'],
            'items' => ['array', 'object']
        ];

        foreach ($values as $key => $required) {
            $this->$key = null;
            $this->getValue($schema, $key, $this->$key, $required);
        }

        if ($this->items === null) {
            $this->items = [];
        }
    }

    protected function validateObjectItems($schema, $start = 0)
    {
        for ($i = $start; $i < $this->dataCount; ++$i) {
            $this->manager->validate($this->data[$i], $schema, strval($i));
        }
    }

    protected function checkArrayItems()
    {
        if (false === $this->additionalItems) {
            if ($this->dataCount > $this->itemsCount) {
                $this->addError('contains more elements than are allowed');
            }
        }
    }

    protected function validateArrayItems()
    {
        for ($i = 0; $i < $this->dataCount; ++$i) {

            if ($i < $this->itemsCount) {
                $this->manager->validate($this->data[$i], $this->items[$i], strval($i));
            } else {

                if (is_object($this->additionalItems)) {
                    $this->validateObjectItems($this->additionalItems, $i);
                }

                break;
            }
        }
    }
}
