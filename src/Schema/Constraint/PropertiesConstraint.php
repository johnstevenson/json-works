<?php declare(strict_types=1);

/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Schema\Constraint;

use \stdClass;

use JohnStevenson\JsonWorks\Helpers\Utils;

class PropertiesConstraint extends BaseConstraint implements ConstraintInterface
{
    /** @var array<array{data: mixed, schema: stdClass, key: string}> */
    protected array $children;

    /**
     * @param mixed $data
     * @param stdClass|array<mixed> $schema
     */
    public function validate($data, $schema, ?string $key = null): void
    {
        if (!($schema instanceof stdClass)) {
            $error = Utils::getArgumentError('$schema', 'sdtClass', $schema);
            throw new \InvalidArgumentException($error);
        }

        $additional = $this->getAdditional($schema);
        $this->children = [];

        $this->checkProperties($data, $schema, $additional);

        foreach ($this->children as $child) {
            $this->manager->validate($child['data'], $child['schema'], $child['key']);
        }
    }

    /**
     * @return object|bool
     */
    protected function getAdditional(stdClass $schema)
    {
        $this->getValue($schema, 'additionalProperties', $value, ['object', 'boolean']);

        return $value;
    }

    /**
    * @param mixed $data
    * @param object|bool $additional
    */
    protected function checkProperties($data, stdClass $schema, $additional): void
    {
        $set = (array) $data;

        $this->parseProperties($schema, $set);
        $this->parsePatternProperties($schema, $set);

        if (false === $additional && Utils::arrayNotEmpty($set)) {
            $this->addError('contains unspecified additional properties');
        }

        $this->mergeAdditional($set, $additional);
    }

    /**
    * @param array<string, mixed> $set
    */
    protected function parseProperties(stdClass $schema, array &$set): void
    {
        if (!$this->getSchemaProperties($schema, 'properties', $props)) {
            return;
        }

        foreach ($props as $key => $subSchema) {
            if (array_key_exists($key, $set)) {
                $this->addChild($set[$key], $subSchema, $key);
                unset($set[$key]);
            }
        }
    }

    /**
    * @param array<string, mixed> $set
    */
    protected function parsePatternProperties(stdClass $schema, array &$set): void
    {
        if (!$this->getSchemaProperties($schema, 'patternProperties', $props)) {
            return;
        }

        foreach ($props as $regex => $subSchema) {
            $this->checkPattern($regex, $subSchema, $set);
        }
    }

    /**
    * @param mixed $value Set by method
    */
    protected function getSchemaProperties(stdClass $schema, string $key, &$value): bool
    {
        if ($result = $this->getValue($schema, $key, $value, ['object'])) {
            $this->manager->dataChecker->checkContainerTypes($value, 'object');
        }

        return $result;
    }

    /**
    * @param array<string, mixed> $set
    * @param object|bool $additional
    */
    protected function mergeAdditional(array $set, $additional): void
    {
        if ($additional instanceof stdClass) {

            foreach ($set as $key => $data) {
                $this->addChild($data, $additional, $key);
            }
        }
    }

    /**
    * @param array<string, mixed> $set
    */
    protected function checkPattern(string $regex, stdClass $schema, array &$set): void
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

    /**
    * @param mixed $data
    */
    protected function addChild($data, stdClass $schema, string $key): void
    {
        $this->children[] = [
            'data' => $data,
            'schema' => $schema,
            'key' => $key
        ];
    }
}
