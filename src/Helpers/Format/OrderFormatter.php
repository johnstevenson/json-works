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

use JohnStevenson\JsonWorks\Utils;

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
        if (is_object($data) && ($properties = Utils::get($schema, 'properties'))) {
            $result = array();

            foreach ($properties as $key => $value) {
                if (isset($data->$key)) {
                    $result[$key] = $this->order($data->$key, $properties->$key);
                    unset($data->$key);
                }
            }
            $result = (object) array_merge($result, (array) $data);

        } elseif (is_array($data) && ($items = Utils::get($schema, 'items'))) {
            $result = array();
            $objSchema = is_object($schema->items) ? $schema->items : null;

            foreach ($data as $item) {
                $itemSchema = $objSchema ?: (next($schema->items) ?: null);
                $result[] = $this->order($item, $itemSchema);
            }

        } else {
            $result = $data;
        }

        return $result;
    }
}
