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

class ContainerConstraint extends BaseConstraint
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
    * @param string $key
    */
    public function validate($data, $schema, $key)
    {
        $this->additional = $this->getAdditional($schema, $key);
        $this->children = [];

        $this->checkProperties($data, $schema);

        foreach ($this->children as $child) {
            $this->manager->validate($child['data'], $child['schema'], $child['key']);
        }
    }

    protected function getAdditional($schema, $key)
    {
        $key = sprintf('additional%s', ucfirst($key));
        $this->getValue($schema, $key, $value, ['object', 'boolean']);

        if (is_bool($value)) {
            return $value === false ? $value : new \stdClass();
        } else {
            return is_object($value) ? $value : new \stdClass();
        }
    }

    protected function checkProperties($data, $schema)
    {
        $set = (array) $data;

        $this->parseProperties($schema, $set);
        $this->parsePatternProperties($schema, $set);

        if (!$this->additional && !empty($set)) {
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
