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

use JohnStevenson\JsonWorks\Schema\Constraints\Manager;
use JohnStevenson\JsonWorks\Schema\Constraints\MaxMinConstraint;

class ObjectConstraint extends BaseConstraint
{
    protected $maxMin;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->maxMin = new MaxMinConstraint($manager);
    }

    public function validate($data, $schema)
    {
        if (0 === count((array) $schema)) {
            return;
        }

        $this->checkCommon($data, $schema);

        $this->checkAllProperties($data, $schema, $children, $additional);

        foreach ($children as $child) {
            $this->manager->validate($child['data'], $child['schema'], $child['key']);
        }
    }

    protected function checkCommon($data, $schema)
    {
        // maxProperties
        $this->maxMin->validate($data, $schema, 'maxProperties');

        // minProperties
        $this->maxMin->validate($data, $schema, 'minProperties');

        // required
        $this->checkRequired($data, $schema);
    }

    protected function checkRequired($data, $schema)
    {
        if (!$this->getValue($schema, 'required', $value, 'array')) {
            return;
        }

        $this->manager->dataChecker->checkArray($value, 'required');

        foreach ($value as $name) {

            if (!property_exists($data, $name)) {
                $this->addError(sprintf("is missing required property '%s'", $name));
            }
        }
    }

    protected function checkAllProperties($data, $schema, &$children, &$additional)
    {
        $set = (array) $data;
        $children = [];

        if (!$this->getValue($schema, 'additionalProperties', $additional, ['object', 'boolean'])) {
            $additional = true;
        }

        $this->parseProperties($schema, $set, $children);
        $this->parsePatternProperties($schema, $set, $children);

        if (!empty($set)) {

            if (!$additional) {
                $this->addError('contains unspecified additional properties');
            } elseif (is_object($additional)) {
                $this->mergeAdditional($set, $additional);
            }
        }
    }

    protected function getSchemaProperties($schema, $key, &$value)
    {
        if ($result = $this->getValue($schema, $key, $value, 'object')) {
            $this->manager->dataChecker->checkObject($value, 'object');
        }

        return $result;
    }

    protected function parseProperties($schema, array &$set, array &$children)
    {
        if (!$this->getSchemaProperties($schema, 'properties', $p)) {
            return;
        }

        foreach ($p as $key => $schema) {

            if (array_key_exists($key, $set)) {
                $children[]= ['data' => $set[$key], 'schema' => $schema, 'key' => $key];
                unset($set[$key]);
            }
        }
    }

    protected function parsePatternProperties($schema, array &$set, array &$children)
    {
        if (!$this->getSchemaProperties($schema, 'patternProperties', $pp)) {
            return;
        }

        foreach ($pp as $regex => $schema) {
            $this->checkPattern($regex, $schema, $set, $children);
        }
    }

    protected function mergeAdditional(array $set, $additional)
    {
        foreach ($set as $key => $value) {
            $children[]= ['data' => $value, 'schema' => $additional, 'key' => $key];
        }
    }

    protected function checkPattern($regex, $schema, array &$set, array &$children)
    {
        $copy = $set;

        foreach ($copy as $key => $value) {

            $matchKey = $key !== '_empty_' ? $key : '';

            if ($this->matchPattern($regex, $matchKey)) {
                $children[]= ['data' => $value, 'schema' => $schema, 'key' => $key];
                unset($set[$key]);
            }
        }
    }
}
