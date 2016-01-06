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
        return $this->order($data, $schema);
    }

    protected function order($data, $schema)
    {
        if ($this->isObjectWithSchema($data, $schema, $properties)) {
            return $this->orderObject($data, $properties);
        }

        if ($this->isArrayWithSchema($data, $schema)) {
            return $this->orderArray($data, $schema);
        }

        return $data;
    }

    protected function isArrayWithSchema($data, $schema)
    {
        return is_array($data) && isset($schema->items);
    }

    protected function isObjectWithSchema($data, $schema, &$properties)
    {
        $result = false;

        if (is_object($data) && isset($schema->properties)) {
            $properties = $schema->properties;
            $result = true;
        }

        return $result;
    }

    protected function orderArray($data, $schema)
    {
        $result = array();
        $objSchema = is_object($schema->items) ? $schema->items : null;

        foreach ($data as $item) {
            $itemSchema = $objSchema ?: (next($schema->items) ?: null);
            $result[] = $this->order($item, $itemSchema);
        }

        return $result;
    }

    protected function orderObject($data, $properties)
    {
        $result = array();

        foreach ($properties as $key => $value) {
            if (isset($data->$key)) {
                $result[$key] = $this->order($data->$key, $properties->$key);
                unset($data->$key);
            }
        }

        return (object) array_merge($result, (array) $data);
    }
}
