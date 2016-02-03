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

class Comparer
{
    public function equals($value1, $value2)
    {
        $type = $this->getJsonType($value1);

        if ($type !== $this->getJsonType($value2)) {
            return false;
        }

        if (in_array($type, ['array', 'number', 'object'])) {
            $method = 'equals' . ucfirst($type);
            return $this->$method($value1, $value2);
        }

        return $value1 === $value2;
    }

    public function uniqueArray(array $data)
    {
        $count = count($data);

        for ($i = 0; $i < $count; ++$i) {

            for ($j = $i + 1; $j < $count; ++$j) {
                if ($this->equals($data[$i], $data[$j])) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function getJsonType($value)
    {
        $result = strtolower(gettype($value));

        if (in_array($result, ['double', 'integer'])) {
            $value = floatval($value);
            $result = 'number';
        }

        return $result;
    }

    protected function equalsArray($arr1, $arr2)
    {
        $count = count($arr1);

        if ($count !== count($arr2)) {
            return false;
        }

        for ($i = 0; $i < $count; ++$i) {
            if (!$this->equals($arr1[$i], $arr2[$i])) {
                return false;
            }
        }

        return true;
    }

    protected function equalsNumber($value1, $value2)
    {
        return 0 === bccomp($value1, $value2, 16);
    }

    protected function equalsObject($obj1, $obj2)
    {
        // get_object_vars fails on objects with digit keys
        if (count((array) $obj1) !== count((array) $obj2)) {
            return false;
        }

        foreach ($obj1 as $key => $value) {
            if (!$this->hasEqualProperty($obj2, $key, $value)) {
                return false;
            }
        }

        return true;
    }

    protected function hasEqualProperty($object, $key, $value)
    {
        if (!property_exists($object, $key)) {
            return false;
        }

        return $this->equals($value, $object->$key);
    }
}
