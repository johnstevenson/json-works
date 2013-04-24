<?php

namespace JohnStevenson\JsonWorks\Schema;

use \JohnStevenson\JsonWorks\Utils;

class Constraints
{
    protected $path;
    protected $lax;

    public function __construct($lax)
    {
        $this->lax = $lax;
    }

    public function validate($data, $schema, $key = null)
    {
        $this->path = Utils::pathAdd($this->path, $key);

        $this->validateCommon($data, $schema);
        $type = gettype($data);

        switch ($type) {
            case 'object':
                $this->validateObject($data, $schema);
                break;
            case 'array':
                $this->validateArray($data, $schema);
                break;
            case 'double':
                # no break
            case 'integer':
                $this->validateNumber($data, $schema);
                break;
            case 'string':
                $this->validateString($data, $schema);
                break;
        }
    }

    protected function validateCommon($data, $schema)
    {
        $common = array('enum', 'type', 'allOf', 'anyOf', 'oneOf', 'not');

        foreach ($common as $name) {

            if ($value = Utils::get($schema, $name)) {

                switch ($name) {
                    case 'enum':
                        $this->validateEnum($value, $data);
                        break;
                    case 'type':
                        $this->validateType($value, $data);
                        break;
                    case 'not':
                        $this->validateNot($value, $data);
                        break;
                    default:
                        $this->validateOf($name, $value, $data);
                }
            }
        }
    }

    protected function validateObject($data, $schema)
    {

        # maxProperties
        if (isset($schema->maxProperties)) {
            $this->validateMaxMin($data, $schema->maxProperties, true);
        }

       if (!$this->lax) {

            # minProperties
            if (isset($schema->minProperties)) {
                $this->validateMaxMin($data, $schema->minProperties, false);
            }

            if (isset($schema->required)) {
                 foreach ((array) $schema->required as $name) {
                    if (!isset($data->$name)) {
                        $this->throwError(sprintf("is missing required property '%s'", $name));
                    }
                }
            }

        }

        # additionalProperties
        $additional = Utils::get($schema, 'additionalProperties', true);

        if (false === $additional) {
            $this->validateObjectWork($data, $schema);
        }

        $this->validateObjectChildren($data, $schema, $additional);
    }

    protected function validateArray($data, $schema)
    {

        # maxItems
        if (isset($schema->maxItems)) {
            $this->validateMaxMin($data, $schema->maxItems, true);
        }

        if (!$this->lax) {

            # minItems
            if (isset($schema->minItems)) {
                $this->validateMaxMin($data, $schema->minItems, false);
            }
        }

        # uniqueItems
        if ($value = Utils::get($schema, 'uniqueItems', false)) {
            if (!Utils::uniqueArray($data, true)) {
                $this->throwError('contains duplicate values');
            }
        }

        # items
        $items = Utils::get($schema, 'items', array());

        # additionalItems
        $additional = Utils::get($schema, 'additionalItems', true);

        if (false === $additional && is_array($items)) {
            if (count($data) > count($items)) {
                $this->throwError('contains more elements than are allowed');
            }
         }

        $this->validateArrayChildren($data, $items, $additional);
    }

    protected function validateNumber($data, $schema)
    {
        # maximum
        if (isset($schema->maximum)) {
            $max = $schema->maximum;

            if ($exclusive = Utils::get($schema, 'exclusiveMaximum', false)) {
                $valid = $data < $max;
            } else {
                $valid = $data <= $max;
            }

            if (!$valid) {
                $error = 'value must be less than ';
                $error .= $exclusive ? 'or equal to ' : '';
                $this->throwError($error.$max);
            }
        }

        # minimum
        if (isset($schema->minimum)) {
            $min = $schema->minimum;

            if ($exclusive = Utils::get($schema, 'exclusiveMinimum', false)) {
                $valid = $data > $min;
            } else {
                $valid = $data >= $min;
            }

            if (!$valid) {
                $error = 'value must be greater than ';
                $error .= $exclusive ? '' : 'or equal to ';
                $this->throwError($error.$min);
            }
        }
    }

    protected function validateString($data, $schema)
    {
        # maxLength
        if (isset($schema->maxLength)) {
            if (strlen($data) > $schema->maxLength) {
                $this->throwError(sprintf('has too many characters, maximum (%d)', $schema->maxLength));
            }
        }

        # minLength
        if (isset($schema->minLength)) {
            if (strlen($data) < $schema->minLength) {
                $this->throwError(sprintf('has too few characters, minimum (%d)', $schema->minLength));
            }
        }

        # pattern
        if (isset($schema->pattern)) {
            if (!$this->match($schema->pattern, $data)) {
                $this->throwError(sprintf('does not match pattern: %s', $schema->pattern));
            }
        }

        # format
        if (isset($schema->format)) {
            $this->validateFormat($data, $schema->format);
        }
    }

    protected function validateEnum($enum, $data)
    {
        $result = false;

        foreach ((array) $enum as $value) {
            if ($result = Utils::equals($value, $data)) {
                break;
            }
        }

        if (!$result) {
            $this->throwError('value not found in enumeration '.json_encode($enum));
        }
    }

    protected function validateType($type, $data)
    {
        $types = (array) $type;
        $result = false;

        foreach ($types as $type) {
            if (Utils::checkType($type, $data)) {
                $result = true;
                break;
            }
        }

        if (!$result) {
            $this->throwError(sprintf("value must be of type '%s'", implode(', ', $types)));
        }
    }

    protected function validateNot($not, $data)
    {
        if ($this->validateChild($data, $not))
        {
            $this->throwError('must not validate against this schema');
        }
    }

    protected function validateOf($type, $value, $data)
    {
        $matches = 0;
        $result = false;

        foreach ($value as $schema) {

            if ($this->validateChild($data, $schema)) {
                ++$matches;
                if ('anyOf' === $type) {
                    break;
                }
            }
        }

        switch ($type) {
            case 'allOf':
                $result = $matches === count($value);
                break;
            case 'anyOf':
                $result = $matches >= 1;
                break;
            case 'oneOf':
                $result = 1 === $matches;
                break;
        }

        if (!$result) {
            $this->throwError('does not match schema requirements');
        }
    }

    protected function validateMaxMin($data, $value, $isMax)
    {
        $count = count((array) $data);

        if ($isMax && $count > $value) {
            $error = 'has too many members, maximum %d';
        } elseif (!$isMax && $count < $value) {
            $error = 'has too few members, minimum (%d)';
        }

        if (isset($error)) {
            $this->throwError(sprintf($error, $value));
        }
    }

    protected function validateObjectWork($data, $schema)
    {
        $set = (array) $data;
        $p = Utils::get($schema, 'properties', new \stdClass());

        foreach ($p as $key => $value) {
            if (isset($set[$key])) {
                unset($set[$key]);
            }
        }

        $pp = Utils::get($schema, 'patternProperties', new \stdClass());
        $setCopy = $set;

        foreach ($setCopy as $key => $value) {

             foreach ($pp as $regex => $val) {
                if ($this->match($regex, $key)) {
                    unset($set[$key]);
                    break;
                }
            }
        }

        if ($set) {
            $this->throwError('contains unspecified additional properties');
        }
    }

    protected function validateObjectChildren($data, $schema, $additional)
    {
        if (true === $additional) {
            $additional = new \stdClass();
        }

        $p = Utils::get($schema, 'properties', new \stdClass());
        $pp = Utils::get($schema, 'patternProperties', new \stdClass());

        foreach ($data as $key => $value) {

            $child = array();

            if (isset($p->$key)) {
                $child[] = $p->$key;
            }

            foreach ($pp as $regex => $val) {
                if ($this->match($regex, $key)) {
                    $child[] = $val;
                }
            }

            if (!$child && $additional) {
                $child[] = $additional;
            }

            foreach($child as $subSchema) {
                $this->validateChild($value, $subSchema, $key);
            }
        }
    }

    protected function validateArrayChildren($data, $items, $additional)
    {
        if (null === $items) {
            $items = new \stdClass();
        }

        if (true === $additional) {
            $additional = new \stdClass();
        }

        $single = is_object($items);
        $dataCount = count($data);
        $itemsCount = !$single ? count($items) : 0;

        for ($i = 0; $i < $dataCount; ++$i) {

            if ($single) {
                $subSchema = $items;
            } elseif ($i < $itemsCount) {
                $subSchema = $items[$i];
            } elseif ($additional) {
                $subSchema = $additional;
            } else {
                continue;
            }

            $this->validateChild($data[$i], $subSchema, strval($i));
        }
    }

    protected function validateChild($data, $schema, $key = null)
    {
        $result = true;
        $currentPath = $this->path;

        if (is_null($key)) {
            try {
                $this->validate($data, $schema);
            } catch (ValidationException $e) {
                $result = false;
            }
        } else {
            $this->validate($data, $schema, $key);
        }

        $this->path = $currentPath;

        return $result;
    }

    protected function validateFormat($data, $format)
    {
        switch ($format) {

            case 'date-time':
                $p = '/^\d{4}-\d{2}-\d{2}[T| ]\d{2}:\d{2}:\d{2}(\.\d{1})?(Z|[\+|-]\d{2}:\d{2})?$/i';
                if (!preg_match($p, $data, $match) || false === strtotime($data)) {
                    $this->throwError('Invalid date-time, '.json_encode($data));
                }
                break;

            case 'email':
                if (null === filter_var($data, FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE)) {
                    $this->throwError('Invalid email, '.json_encode($data));
                }
                break;

            case 'hostname':
                if (!preg_match('/^[_a-z]+\.([_a-z]+\.?)+$/i', $data)) {
                    $this->throwError('Invalid hostname, '.json_encode($data));
                }
                break;

            case 'ipv4':
                if (null === filter_var($data, FILTER_VALIDATE_IP, FILTER_NULL_ON_FAILURE | FILTER_FLAG_IPV4)) {
                    $this->throwError('Invalid IPv4 address, '.json_encode($data));
                }
                break;

            case 'ipv6':
                if (null === filter_var($data, FILTER_VALIDATE_IP, FILTER_NULL_ON_FAILURE | FILTER_FLAG_IPV6)) {
                    $this->throwError('Invalid IPv6 address, '.json_encode($data));
                }
                break;

            case 'uri':
                if (null === filter_var($data, FILTER_VALIDATE_URL, FILTER_NULL_ON_FAILURE)) {
                    $this->throwError('Invalid uri, '.json_encode($data));
                }
                break;

            default:
                $this->throwError('Unknown format, '.json_encode($data));
        }
    }

    protected function validateUnique($data)
    {
        $count = count($data);
        for ($i = 0; $i < $count; ++$i) {
            for ($j = $i + 1; $j < $count; ++$j) {
                if (Utils::equals($data[$i], $data[$j])) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function match($regex, $string)
    {
         return preg_match('/'.$regex.'/', $string, $match);
    }

    protected function throwError($msg)
    {
        $path = $this->path ?: '#';
        $error = sprintf("Property: %s. Error: %s", $path, $msg);
        throw new ValidationException($error);
    }
}
