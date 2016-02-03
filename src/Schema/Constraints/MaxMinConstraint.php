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

class MaxMinConstraint extends BaseConstraint
{

    protected function run($data, $schema, $key = null)
    {
        if (!$this->getInteger($schema, $key, $value)) {
            return;
        }

        $max = preg_match('/^max/', $key);
        $this->checkMaxMin($data, $value, $max);
    }

    protected function checkMaxMin($data, $value, $max)
    {
        $count = count((array) $data);
        $error = null;

        if ($max && $count > $value) {
            $error = "has too many %d, maximum '%d'";
        } elseif (!$max && $count < $value) {
            $error = "has too few %d, minimum '%d'";
        }

        if ($error) {
            $name = is_object($data) ? 'properties' : 'items';
            $this->addError(sprintf($error, $name, $value));
        }
    }

    protected function getInteger($schema, $key, &$value)
    {
        if ($result = $this->getValue($schema, $key, $value, $type)) {

            if ($type !== 'integer') {
                $this->throwSchemaError('integer', $value);
            }

            if ($value < 0) {
                $this->throwSchemaError('>= 0', $value);
            }
        }

        return $result;
    }
}
