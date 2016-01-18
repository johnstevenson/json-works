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
* A class to order elements and return an unreferenced copy of the data
*/
class Orderer extends BaseOrder
{
    /**
    * Reorders object properties using the schema order
    *
    * @internal
    * @param mixed $data
    * @param stdClass $schema
    * @return mixed
    */
    public function run($data, stdClass $schema)
    {
        if ($properties = $this->objectWithSchema($data, $schema)) {
            return $this->orderObject($data, $properties);
        }

        if ($items = $this->arrayWithSchema($data, $schema, $object)) {

            if ($object) {
                return $this->orderArraySingleSchema($data, $items);
            }

            return $this->orderArrayMultiSchema($data, $items);
        }

        return $data;
    }

    protected function orderArrayMultiSchema($data, $items)
    {
        $result = [];

        $properties = $this->getArraySchema($items);

        foreach ($properties as $key => $schema) {

            if ($item = $this->findPropertyInArray($data, $key)) {
                $result[] = (object) array($key => $this->run($item, $schema));
            }

            if (empty($data)) {
                break;
            }
        }

        return array_merge($result, $data);
    }

    /**
    * Orders an array using the schema items properties
    *
    * @param mixed $data
    * @param stdClass $schema
    * @return mixed[]
    */
    protected function orderArraySingleSchema($data, stdClass $schema)
    {
        $result = [];

        foreach ($data as $item) {
            $result[] = $this->run($item, $schema);
        }

        return $result;
    }

    /**
    * Orders an object using the schema properties
    *
    * @param mixed $data
    * @param stdClass $properties
    * @return stdClass
    */
    protected function orderObject($data, stdClass $properties)
    {
        $result = [];

        foreach ($properties as $key => $schema) {
            if (property_exists($data, $key)) {
                $result[$key] = $this->run($data->$key, $schema);
                unset($data->$key);
            }
        }

        return (object) array_merge($result, (array) $data);
    }
}
