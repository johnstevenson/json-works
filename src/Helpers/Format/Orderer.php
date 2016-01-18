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
* A class to order elements based on their order in a schema
*/
class Orderer
{
    /**
    * Reorders object properties using the schema order
    *
    * @internal
    * @param mixed $data
    * @param mixed $schema
    * @return mixed
    */
    public function run($data, $schema)
    {
        if ($properties = $this->objectWithSchema($data, $schema)) {
            return $this->orderObject($data, $properties);
        }

        if ($items = $this->arrayWithSchema($data, $schema)) {
            return $this->orderArray($data, $items);
        }

        return $data;
    }

    /**
    * Returns schema items if they are relevant and exist
    *
    * @param mixed $data
    * @param mixed $schema
    * @return null|stdClass
    */
    protected function arrayWithSchema($data, $schema)
    {
        if (is_array($data)) {
            return $this->getProperties($schema, 'items');
        }
    }

    /**
    * Returns schema properties if they are relevant and exist
    *
    * @param mixed $data
    * @param mixed $schema
    * @return mixed
    */
    protected function objectWithSchema($data, $schema)
    {
        if (is_object($data)) {
            return $this->getProperties($schema, 'properties');
        }
    }

    /**
    * Returns a schema properties is valid
    *
    * @param mixed $schema
    * @param string $key
    * @return object|null
    */
    protected function getProperties($schema, $key)
    {
        if (isset($schema->$key)) {
            return is_object($schema->$key) ? $schema->$key : null;
        }
    }


    /**
    * Orders an array using the schema items properties
    *
    * @param mixed $data
    * @param object $schema
    * @return mixed[]
    */
    protected function orderArray($data, $schema)
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
    * @param object $properties
    * @return object
    */
    protected function orderObject($data, $properties)
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
