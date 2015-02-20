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
            $result = is_integer($value) || (is_float($value) && $value == round($value));
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
                    if (Utils::equals($data[$i], $data[$j])) {
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

    public static function pathAdd($path, $key)
    {
        if (strlen($encoded = static::pathEncodeKey($key))) {
            $encoded = '/'.$encoded;
        }

        return $path.$encoded;
    }

    public static function pathDecode($path)
    {
        $keys = explode('/', $path);
        array_shift($keys);

        foreach ($keys as &$value) {
            $value = str_replace('~0', '~', str_replace('~1', '/', $value));
        }

        return $keys;
    }

    public static function pathEncode($keys)
    {
        $result = '';
        foreach ((array) $keys as $value) {
            $result = static::pathAdd($result, $value);
        }

        return $result;
    }

    public static function pathEncodeKey($key)
    {
        return str_replace('/', '~1', str_replace('~', '~0', strval($key)));
    }

    public static function dataCopy($data, $callback = null)
    {
        if ($callback) {
            $data = call_user_func_array($callback, array($data));
        }

        if (($object = is_object($data)) || is_array($data)) {

            $result = array();

            foreach ($data as $key => $value) {
                $object = $object ?: is_string($key);
                $result[$key] = static::dataCopy($value, $callback);
            }

            $result = $object ? (object) $result: $result;

        } else {
            $result = $data;
        }

        return $result;
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

    /**
    * Encodes data into JSON
    *
    * @param mixed $data The data to be encoded
    * @param boolean $pretty Format the output
    * @return string Encoded json
    */
    public static function dataToJson($data, $pretty)
    {
        $newLine = $pretty ? chr(10) : null;

        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $pprint = $pretty ? JSON_PRETTY_PRINT : 0;
            $options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | $pprint;
            return static::finalizeJson(json_encode($data, $options), $newLine);
        }

        $json = json_encode($data);

        $len = strlen($json);
        $result = $string = '';
        $inString = $escaped = false;
        $level = 0;
        $space = $pretty ? chr(32) : null;
        $convert = function_exists('mb_convert_encoding');

        for ($i = 0; $i < $len; $i++) {
            $char = $json[$i];

            # are we inside a json string?
            if ('"' === $char && !$escaped) {
                $inString = !$inString;
            }

            if ($inString) {
                $string .= $char;
                $escaped = '\\' === $char ? !$escaped : false;

                continue;

            } elseif ($string) {
                # end of the json string
                $string .= $char;

                # unescape slashes
                $string = str_replace('\\/', '/', $string);

                # unescape unicode
                if ($convert) {
                    $string = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function($match) {
                        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
                    }, $string);
                }

                $result .= $string;
                $string = '';

                continue;
            }

            if (':' === $char) {
                # add space after colon
                $char .= $space;
            } elseif (strpbrk($char, '}]')) {
                # char is an end element, so add a newline
                $result .= $newLine;
                # decrease indent level
                $level--;
                $result .= str_repeat($space, $level * 4);
            }

            $result .= $char;

            if (strpbrk($char, ',{[')) {
                # char is a start element, so add a newline
                $result .= $newLine;

                # increase indent level if not a comma
                if (',' !== $char) {
                    $level++;
                }

                $result .= str_repeat($space, $level * 4);
            }
        }

        return static::finalizeJson($result, $newLine);
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

    private static function finalizeJson($json, $newline)
    {
        if ($newline) {
            # collapse empty {} and []
            $json = preg_replace_callback('#(\{\s+\})|(\[\s+\])#', function($match) {
                return $match[1] ? '{}' : '[]';
            }, $json);

            $json .= $newline;
        }

        return $json;
    }
}
