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
        $schemas = $this->setDetails($schema, $key);
        $matches = $this->getMatches($data, $schemas);

        if (!$this->checkResult($key, $matches, count($schemas))) {
            $this->addError($this->getError($key));
        }
    }

    protected function setDetails($schema, $key)
    {
        $this->type = $key === 'not' ? 'object' : 'array';
        $this->matchFirst = in_array($key, ['anyOf', 'not']);

        return $this->type === 'object' ? [$schema] : $schema;
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

    protected function getError($key)
    {
        if ($key === 'not') {
            return 'must not validate against this schema';
        }

        return sprintf("does not match '%s' schema requirements", $key);
    }
}
