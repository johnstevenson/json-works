<?php declare(strict_types=1);
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Schema;

class Comparer extends JsonTypes
{
    /**
     * @param mixed $value1
     * @param mixed $value2
     */
    public function equals($value1, $value2): bool
    {
        $type = $this->getGeneric($value1);

        if ($type !== $this->getGeneric($value2)) {
            return false;
        }

        if ($type === 'array') {
            return $this->equalsArray($value1, $value2);
        }

        if ($type === 'number') {
            return $this->equalsNumber($value1, $value2);
        }

        if ($type === 'object') {
            return $this->equalsObject($value1, $value2);
        }

        return $value1 === $value2;
    }

    /**
     * @param array<mixed> $data
     */
    public function uniqueArray(array $data): bool
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

    /**
     * @param array<mixed> $arr1
     * @param array<mixed> $arr2
     */
    protected function equalsArray(array $arr1, array $arr2): bool
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

    /**
     * @param int|double|string $value1
     * @param int|double|string $value2
     */
    protected function equalsNumber($value1, $value2): bool
    {
        return 0 === bccomp(strval($value1), strval($value2), 16);
    }

   /**
     * @param object $obj1
     * @param object $obj2
     */
    protected function equalsObject($obj1, $obj2): bool
    {
        // get_object_vars fails on objects with digit keys
        if (count((array) $obj1) !== count((array) $obj2)) {
            return false;
        }

        foreach (get_object_vars($obj1) as $key => $value) {
            if (!$this->hasEqualProperty($obj2, $key, $value)) {
                return false;
            }
        }

        return true;
    }

   /**
     * @param object $object
     * @param mixed $value
     */
    protected function hasEqualProperty($object, string $key, $value): bool
    {
        if (!property_exists($object, $key)) {
            return false;
        }

        // @phpstan-ignore-next-line
        return $this->equals($value, $object->$key);
    }
}
