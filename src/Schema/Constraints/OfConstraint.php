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

use JohnStevenson\JsonWorks\Schema\ValidationException;

class OfConstraint extends BaseConstraint
{
    protected $type;
    protected $matchFirst;

    public function validate($data, $schema, $key)
    {
        $this->setDetails($key);
        $schemas = $this->checkSchema($schema);
        $matches = $this->getMatches($data, $schemas);

        if (!$this->checkResult($key, $matches, count($schemas))) {
            $this->addError($this->getError($key));
        }
    }

    protected function setDetails($key)
    {
        $this->type = $key === 'not' ? 'object' : 'array';
        $this->matchFirst = in_array($key, ['anyOf', 'not']);
    }

    protected function getMatches($data, $schemas)
    {
        $result = 0;

        foreach ($schemas as $subSchema) {

            if ($this->testChild($data, $subSchema)) {
                ++$result;

                if ($this->matchFirst) {
                    break;
                }
            }
        }

        return $result;
    }

    protected function testChild($data, $schema)
    {
        $currentStop = $this->manager->stopOnError;
        $this->manager->stopOnError = true;

        try {
            $this->manager->validate($data, $schema);
            $result = true;
        } catch (ValidationException $e) {
            $result = false;
            array_pop($this->manager->errors);
        }

        $this->manager->stopOnError = $currentStop;
        return $result;
    }

    protected function checkResult($key, $matches, $schemaCount)
    {
        switch ($key) {
            case 'allOf':
                return $matches === $schemaCount;
            case 'anyOf':
                return $matches !== 0;
            case 'oneOf':
                return $matches === 1;
        }

        return $matches === 0;
    }

    protected function checkSchema($schema)
    {
        if ($this->type === gettype($schema)) {
            return $this->type === 'object' ? [$schema] : $this->checkArray($schema);
        }

        $error = $this->getSchemaError($this->type, gettype($schema));
        throw new \RuntimeException($error);
    }

    protected function checkArray(array $schema)
    {
        if (count($schema) > 0) {
            return $schema;
        }

        $error = $this->getSchemaError('> 0', '0');
        throw new \RuntimeException($error);
    }

    protected function getError($key)
    {
        if ($key === 'not') {
            return 'must not validate against this schema';
        }

        return sprintf("does not match '%s' schema requirements", $key);
    }
}
