<?php
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Helpers\Format;

/**
* A class to order elements and return an unreferenced copy of the data
*/
class OrderFormatter extends BaseFormatter
{
    public function run($data, $schema)
    {
        if ($properties = $this->objectWithSchema($data, $schema)) {
            return $this->orderObject($data, $properties);
        }

        if ($items = $this->arrayWithSchema($data, $schema, $isObject)) {

            if ($isObject) {
                return $this->orderArraySingleSchema($data, $items);
            }

            return $this->orderArrayMultiSchema($data, $items);
        }

        return $data;
    }

    protected function arrayWithSchema($data, $schema, &$isObject)
    {
        if (is_array($data) && isset($schema->items)) {

            if ($this->isContainer($schema->items, $isObject)) {
                return $schema->items;
            }
        }
    }

    protected function objectWithSchema($data, $schema)
    {
        if (is_object($data) && isset($schema->properties)) {
            return $schema->properties;
        }
    }

    protected function findObjectInArray(&$data, $key)
    {
        for ($i = 0; $i < count($data); ++$i) {
            $item = $data[$i];

            if (is_object($item) && property_exists($item, $key)) {
                unset($data[$i]);
                return $this->getFirstObject($item, $dummy);
            }
        }
    }

    protected function getArraySchema($items)
    {
        $result = new \stdClass();

        foreach ($items as $item) {
            if ($properties = $this->objectWithSchema($item, $item)) {
                $result->$key = $this->getFirstObject($properties, $key);
            }
        }

        return $result;
    }

    protected function getFirstObject($item, &$key)
    {
        foreach ($item as $key => $value) {
            return $item->$key;
        }
    }

    protected function orderArrayMultiSchema($data, $items)
    {
        $result = array();

        $properties = $this->getArraySchema($items);

        foreach ($properties as $key => $schema) {

            if ($item = $this->findObjectInArray($data, $key)) {
                $result[] = (object) array($key => $this->run($item, $schema));
            }

            if (empty($data)) {
                break;
            }
        }

        return array_merge($result, $data);
    }

    protected function orderArraySingleSchema($data, $schema)
    {
        $result = array();

        foreach ($data as $item) {
            $result[] = $this->run($item, $schema);
        }

        return $result;
    }

    protected function orderObject($data, $properties)
    {
        $result = array();

        foreach ($properties as $key => $schema) {
            if (property_exists($data, $key)) {
                $result[$key] = $this->run($data->$key, $schema);
                unset($data->$key);
            }
        }

        return (object) array_merge($result, (array) $data);
    }
}
