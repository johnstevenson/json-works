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
}
