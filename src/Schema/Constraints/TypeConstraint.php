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

class TypeConstraint extends BaseConstraint
{
    protected function run($data, $type, $key = null)
    {
        $types = (array) $type;
        $result = false;

        foreach ($types as $type) {
            if ($result = $this->checkType($data, $type)) {
                break;
            }
        }

        if (!$result) {
            $error = sprintf("value must be of type '%s'", implode(', ', $types));
            $this->addError($error);
        }
    }

    protected function checkType($data, $type)
    {
        if (method_exists($this, $method = 'is' . ucfirst($type))) {
            return $this->$method($data);
        }

        if (in_array($type, ['object', 'array', 'string', 'null'])) {
            return call_user_func('is_' . $type, $data);
        }
    }

    protected function isBoolean($data)
    {
        return is_bool($data);
    }

    protected function isInteger($data)
    {
        // Large integers may be stored as a float (Issue:1). Note that data
        // may have been truncated to fit a 64-bit PHP_MAX_INT
        return is_integer($data) || (is_float($data) && $data === floor($data));
    }

    protected function isNumber($data)
    {
        return is_float($data) || is_integer($data);
    }
}
