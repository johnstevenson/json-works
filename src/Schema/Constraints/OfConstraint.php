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

class OfConstraint extends BaseConstraint
{
    protected $type;
    protected $matchFirst;
    protected $error;

    protected function run($data, $schema, $key = null)
    {
        $this->setDetails($key);
        $schemas = $this->checkSchema($schema);
        $matches = $this->getMatches($data, $schemas);

        if (!$this->checkResult($key, $matches, count($schemas))) {
            $this->addError($this->error);
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

            if ($this->validateChild($data, $subSchema)) {
                ++$result;

                if ($this->matchFirst) {
                    break;
                }
            }
        }

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
        if ($this->type !== gettype($schema)) {
            $this->throwSchemaError($this->type, gettype($schema));
        }

        return $this->type === 'object' ? [$schema] : $schema ;
    }
}
