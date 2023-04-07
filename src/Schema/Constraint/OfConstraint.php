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
use JohnStevenson\JsonWorks\Schema\ValidationException;

class OfConstraint extends BaseConstraint implements ConstraintInterface
{
    /**
     * @param mixed $data
     * @param stdClass|array<mixed> $schema
     */
    public function validate($data, $schema, ?string $key = null): void
    {
        if (!is_string($key)) {
            $error = Utils::getArgumentError('$key', 'string', $key);
            throw new \InvalidArgumentException($error);
        }

        $matchFirst = in_array($key, ['anyOf', 'not'], true);
        $schemas = is_array($schema) ? $schema : [$schema];
        $matches = $this->getMatches($data, $schemas, $matchFirst);

        if (!$this->checkResult($key, $matches, count($schemas))) {
            $this->addError($this->getError($key));
        }
    }

    /**
     * @param mixed $data
     * @param array<mixed> $schemas
     */
    protected function getMatches($data, array $schemas, bool $matchFirst): int
    {
        $result = 0;

        foreach ($schemas as $subSchema) {
            // type check
            if (!($subSchema instanceof stdClass)) {
                $error = Utils::getArgumentError('$subSchema', 'stdClass', $subSchema);
                throw new \InvalidArgumentException($error);
            }

            if ($this->testChild($data, $subSchema)) {
                ++$result;

                if ($matchFirst) {
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
