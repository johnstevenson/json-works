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
        if ($this->differentTypes($value1, $value2, $type)) {
            return false;
        }

        if (method_exists($this, $method = 'equals' . ucfirst($type))) {
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

    protected function differentTypes(&$value1, &$value2, &$type1)
    {
        $type1 = gettype($value1);
        $type2 = gettype($value2);

        if ('integer' === $type1 && 'double' === $type2) {
            $value1 = floatval($value1);
            $type1 = 'double';
        } elseif ('integer' === $type2 && 'double' === $type1) {
            $value2 = floatval($value2);
            $type2 = 'double';
        }

        return $type1 !== $type2;
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

    protected function equalsDouble($value1, $value2)
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
            if (!property_exists($obj2, $key) || !$this->equals($value, $obj2->$key)) {
                return false;
            }
        }

        return true;
    }
}
