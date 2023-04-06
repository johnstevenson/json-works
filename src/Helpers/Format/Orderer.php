<?php declare(strict_types=1);
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Helpers\Format;

use \stdClass;

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
    * @return mixed
    */
    public function run($data, ?stdClass $schema)
    {
        $properties = $this->objectWithSchema($data, $schema);

        if ($properties !== null) {
            return $this->orderObject($data, $properties);
        }

        $items = $this->arrayWithSchema($data, $schema);

        if ($items !== null) {
            return $this->orderArray($data, $items);
        }

        return $data;
    }

    /**
    * Returns schema items if they are relevant and exist
    *
    * @param mixed $data
    */
    protected function arrayWithSchema($data, ?stdClass $schema): ?stdClass
    {
        if (!is_array($data)) {
            return null;
        }

        $items = $this->getProperties($schema, 'items');
        return $items ?? new stdClass();
    }

    /**
    * Returns schema properties if they are relevant and exist
    *
    * @param mixed $data
    */
    protected function objectWithSchema($data, ?stdClass $schema): ?stdClass
    {
        if (!is_object($data)) {
            return null;
        }

        $properties = $this->getProperties($schema, 'properties');

        if ($properties === null) {
            $properties = $this->getPropertiesFromData($data);
        }

        return $properties;
    }

    /**
    * Returns schema properties if valid, otherwise null
    *
    */
    protected function getProperties(?stdClass $schema, string $key): ?stdClass
    {
        if (!isset($schema->$key)) {
            return null;
        }

        return ($schema->$key instanceof stdClass) ? $schema->$key : null;
    }

    /**
    * Creates schema properties from ordered data properties
    *
    * @param object $data
    */
    protected function getPropertiesFromData($data): stdClass
    {
        $result = new stdClass();

        $keys = array_keys((array) $data);
        sort($keys, SORT_NATURAL | SORT_FLAG_CASE);

        foreach ($keys as $key) {
            $result->$key = new stdClass();
        }

        return $result;
    }

    /**
    * Orders an array using the schema items properties
    *
    * @param mixed $data
    * @param stdClass $schema
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

        foreach (get_object_vars($properties) as $key => $schema) {
            if (property_exists($data, $key)) {
                // @phpstan-ignore-next-line
                $result[$key] = $this->run($data->$key, $schema);
                // @phpstan-ignore-next-line
                unset($data->$key);
            }
        }

        return (object) array_merge($result, (array) $data);
    }
}
