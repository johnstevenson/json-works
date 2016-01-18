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

use stdClass;

/**
* A class to provide the tools to order elements
*/
class BaseOrder extends BaseFormat
{
    /**
    * Returns schema items if they are relevant and exist
    *
    * @param mixed $data
    * @param stdClass $schema
    * @param bool $object
    * @return null|stdClass
    */
    protected function arrayWithSchema($data, stdClass $schema, &$object)
    {
        if (is_array($data) && isset($schema->items)) {

            if ($this->isContainer($schema->items, $object)) {
                return $schema->items;
            }
        }
    }

    /**
    * Returns schema properties if they are relevant and exist
    *
    * @param mixed $data
    * @param stdClass $schema
    * @return mixed
    */
    protected function objectWithSchema($data, stdClass $schema)
    {
        if (is_object($data) && isset($schema->properties)) {

            if (is_object($schema->properties)) {
                return $schema->properties;
            }
        }
    }

    /**
    * Searches an array of data to match an object property
    *
    * If found, the object is deleted from the data
    *
    * @param array $data
    * @param string $key
    * @return mixed
    */
    protected function findPropertyInArray(array &$data, $key)
    {
        $len = count($data);

        for ($i = 0; $i < $len; ++$i) {
            $item = $data[$i];

            if (is_object($item) && property_exists($item, $key)) {
                unset($data[$i]);
                return $this->getFirstProperty($item, $dummy);
            }
        }
    }

    /**
    * Searches schema items and returns an array of item properties
    *
    * @param array $items
    * @return array
    */
    protected function getArraySchema(array $items)
    {
        $result = [];

        foreach ($items as $item) {
            if ($properties = $this->objectWithSchema($item, $item)) {
                $result[$key] = $this->getFirstProperty($properties, $key);
            }
        }

        return $result;
    }

    /**
    * Returns the value of the first object property
    *
    * @param stdClass $item
    * @param string $rootKey Set by the method
    * @return mixed
    */
    protected function getFirstProperty($item, &$rootKey)
    {
        foreach ($item as $key => $value) {
            $rootKey = $key;
            return $item->$key;
        }
    }
}
