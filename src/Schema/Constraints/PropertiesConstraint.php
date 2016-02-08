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

class PropertiesConstraint extends BaseConstraint
{
    /**
    * @var object|bool
    */
    protected $additional;

    /**
    * @var array
    */
    protected $children;

    /**
    * The main method
    *
    * @param mixed $data
    * @param mixed $schema
    */
    public function validate($data, $schema)
    {
        $this->additional = $this->getAdditional($schema);
        $this->children = [];

        $this->checkProperties($data, $schema);

        foreach ($this->children as $child) {
            $this->manager->validate($child['data'], $child['schema'], $child['key']);
        }
    }

    protected function getAdditional($schema)
    {
        $this->getValue($schema, 'additionalProperties', $value, ['object', 'boolean']);

        return $value;
    }

    protected function checkProperties($data, $schema)
    {
        $set = (array) $data;

        $this->parseProperties($schema, $set);
        $this->parsePatternProperties($schema, $set);

        if (false === $this->additional && !empty($set)) {
            $this->addError('contains unspecified additional properties');
        }

        $this->mergeAdditional($set);
    }

    protected function parseProperties($schema, array &$set)
    {
        if (!$this->getSchemaProperties($schema, 'properties', $p)) {
            return;
        }

        foreach ($p as $key => $schema) {

            if (array_key_exists($key, $set)) {
                $this->addChild($set[$key], $schema, $key);
                unset($set[$key]);
            }
        }
    }

    protected function parsePatternProperties($schema, array &$set)
    {
        if (!$this->getSchemaProperties($schema, 'patternProperties', $pp)) {
            return;
        }

        foreach ($pp as $regex => $schema) {
            $this->checkPattern($regex, $schema, $set);
        }
    }

    protected function getSchemaProperties($schema, $key, &$value)
    {
        if ($result = $this->getValue($schema, $key, $value, 'object')) {
            $this->manager->dataChecker->checkContainerTypes($value, 'object');
        }

        return $result;
    }

    protected function mergeAdditional(array $set)
    {
        if (is_object($this->additional)) {

            foreach ($set as $key => $data) {
                $this->addChild($data, $this->additional, $key);
            }
        }
    }

    protected function checkPattern($regex, $schema, array &$set)
    {
        $copy = $set;

        foreach ($copy as $key => $value) {

            $matchKey = $key !== '_empty_' ? $key : '';

            if ($this->matchPattern($regex, $matchKey)) {
                $this->addChild($value, $schema, $key);
                unset($set[$key]);
            }
        }
    }

    protected function addChild($data, $schema, $key)
    {
        $this->children[] = [
            'data' => $data,
            'schema' => $schema,
            'key' => $key
        ];
    }
}
