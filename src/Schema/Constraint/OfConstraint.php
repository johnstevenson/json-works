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

use JohnStevenson\JsonWorks\Schema\ValidationException;

class OfConstraint extends BaseConstraint
{
    protected string $type;
    protected bool $matchFirst;

    /**
     * @param mixed $data
     * @param object|array<mixed> $schema
     */
    public function validate($data, $schema, string $key): void
    {
        $schemas = $this->setDetails($schema, $key);
        $matches = $this->getMatches($data, $schemas);

        if (!$this->checkResult($key, $matches, count($schemas))) {
            $this->addError($this->getError($key));
        }
    }

    /**
     * @param object|array<mixed> $schema
     * @return array<object>
     */
    protected function setDetails($schema, string $key): array
    {
        $this->type = $key === 'not' ? 'object' : 'array';
        $this->matchFirst = in_array($key, ['anyOf', 'not'], true);

        return $this->type === 'object' ? [$schema] : $schema;
    }

    /**
     * @param mixed $data
     * @param array<object> $schemas
     */
    protected function getMatches($data, array $schemas): int
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

    /**
     * @param mixed $data
     */
    protected function testChild($data, stdClass $schema): bool
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

    protected function checkResult(string $key, int $matches, int $schemaCount): bool
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

    protected function getError(string $key): string
    {
        if ($key === 'not') {
            return 'must not validate against this schema';
        }

        return sprintf("does not match '%s' schema requirements", $key);
    }
}
