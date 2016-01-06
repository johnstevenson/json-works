<?php

namespace JohnStevenson\JsonWorks;

class Utils
{
    public static function get($container, $key, $default = null)
    {
        $result = $default;

        if (is_object($container)) {
            $result = isset($container->$key) ? $container->$key : $default;
        } elseif (is_array($container)) {
            $result = isset($container[$key]) ? $container[$key] : $default;
        }

        return $result;
    }

    public static function checkType($type, $value)
    {
        $result = false;

        if ('number' === $type) {
            $result = is_float($value) || is_integer($value);
        } elseif ('boolean' === $type) {
            $result = is_bool($value);
        } elseif ('integer' === $type) {
            // Large integers may be stored as a float (Issue:1). Note that data
            // may have been truncated to fit a 64-bit PHP_MAX_INT
            $result = is_integer($value) || (is_float($value) && $value === floor($value));
        } elseif (function_exists($func = 'is_'.$type)) {
            $result = call_user_func($func, $value);
        }

        return $result;
    }

    public static function equals($var1, $var2)
    {
        $type1 = gettype($var1);
        $type2 = gettype($var2);

        if ('integer' === $type1 && 'double' === $type2) {
            $var1 = floatval($var1);
            $type1 = 'double';
        } elseif ('integer' === $type2 && 'double' === $type1) {
            $var2 = floatval($var2);
            $type2 = 'double';
        }

        if ($type1 !== $type2) {
            return false;
        }

        if ('object' === $type1) {
            return static::equalsObject($var1, $var2);
        } elseif ('array' === $type1) {
            return static::equalsArray($var1, $var2);
        } elseif ('double' === $type1) {
            return 0 === bccomp($var1, $var2, 16);
        } else {
            return $var1 === $var2;
        }
    }

    public static function uniqueArray($data, $check = false)
    {
        $out = array();
        $count = count($data);
        $equals = array();

        for ($i = 0; $i < $count; ++$i) {
            if (!in_array($i, $equals)) {
                $out[] = $data[$i];

                for ($j = $i + 1; $j < $count; ++$j) {
                    if (static::equals($data[$i], $data[$j])) {
                        $equals[] = $j;
                        if ($check) {
                            return false;
                        }
                    }
                }
            }
        }

        return $check ? true : $out;
    }

    public static function dataPrune($data)
    {
        $props = 0;
        return  static::workPrune($data, $props);
    }

    public static function dataOrder($data, $schema)
    {
        if (is_object($data) && ($properties = Utils::get($schema, 'properties'))) {
            $result = array();

            foreach ($properties as $key => $value) {
                if (isset($data->$key)) {
                    $result[$key] = static::dataOrder($data->$key, $properties->$key);
                    unset($data->$key);
                }
            }
            $result = (object) array_merge($result, (array) $data);

        } elseif (is_array($data) && ($items = Utils::get($schema, 'items'))) {
            $result = array();
            $objSchema = is_object($schema->items) ? $schema->items : null;

            foreach ($data as $item) {
                $itemSchema = $objSchema ?: (next($schema->items) ?: null);
                $result[] = static::dataOrder($item, $itemSchema);
            }

        } else {
            $result = $data;
        }

        return $result;
    }

    protected static function equalsObject($obj1, $obj2)
    {
        # get_object_vars fails on objects with digit keys
        if (count((array) $obj1) !== count((array) $obj2)) {
            return false;
        }

        foreach ($obj1 as $key => $value) {
            if (!isset($obj2->$key) || !static::equals($value, $obj2->$key)) {
                return false;
            }
        }

        return true;
    }

    protected static function equalsArray($arr1, $arr2)
    {
        $count = count($arr1);

        if ($count !== count($arr2)) {
            return false;
        }

        for ($i = 0; $i < $count; ++$i) {
            if (!static::equals($arr1[$i], $arr2[$i])) {
                return false;
            }
        }

        return true;
    }

    protected static function workPrune($data, &$props)
    {
        if (($object = is_object($data)) || is_array($data)) {

            $result = array();
            $currentProps = $props;

            foreach ($data as $key => $value) {
                $object = $object ?: is_string($key);
                $value = static::workPrune($value, $props);

                if ($props > $currentProps) {
                    $result[$key] = $value;
                }
                $props = $currentProps;
            }

            $props = count($result);
            $result = $object ? (object) $result: $result;

        } else {
            ++$props;
            $result = $data;
        }

        return $result;
    }
}
