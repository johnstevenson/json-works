<?php declare(strict_types=1);

/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Schema;

use JohnStevenson\JsonWorks\Helpers\Utils;
use JohnStevenson\JsonWorks\Schema\Comparer;

class DataChecker
{
    protected Comparer $comparer;

    public function __construct()
    {
        $this->comparer = new Comparer();
    }

    /**
     * @param mixed $value
     * @param string|array<string>|null $required
     */
    public function checkType($value, $required): void
    {
        $type = $this->comparer->getSpecific($value);

        if ($required !== null) {
            $types = (array) $required;

            if (!in_array($type, $types, true)) {
                $error = $this->formatError(implode('|', $types), $type);
                throw new \RuntimeException($error);
            }
        }
    }

    /**
     * @param array<mixed> $schema
     */
    public function checkArray(array $schema, string $key): void
    {
        if ($key !== 'type') {
            $this->checkArrayCount($schema);
        }

        $this->checkArrayValues($schema, $key);
    }

    /**
     * @param mixed $schema
     */
    public function checkContainerTypes($schema, string $type): void
    {
        foreach ($schema as $value) {
            if (!$this->comparer->checkType($value, $type)) {
                $error = $this->formatError($type, 'mixed');
                throw new \RuntimeException($error);
            }
        }
    }

    /**
     * @param mixed $schema
     */
    public function emptySchema($schema): bool
    {
        $this->checkType($schema, 'object');

        return Utils::arrayIsEmpty((array) $schema);
    }

    /**
     * @param mixed $schema
     */
    public function checkForRef($schema, ?string &$ref): bool
    {
        if ($result = (is_object($schema) && property_exists($schema, '$ref'))) {
            // @phpstan-ignore-next-line
            $ref = $schema->{'$ref'};
            $this->checkType($ref, 'string');
        }

        return $result;
    }

    public function formatError(string $expected, string $value): string
    {
        return sprintf(
            "Invalid schema value: expected '%s', got '%s'",
            $expected,
            $value
        );
    }

    /**
     * @param array<mixed> $schema
     */
    protected function checkArrayCount(array $schema): void
    {
        if (count($schema) <= 0) {
            $error = $this->formatError('> 0', '0');
            throw new \RuntimeException($error);
        }
    }

    /**
     * @param array<mixed> $schema
     */
    protected function checkArrayValues(array $schema, string $key): void
    {
        if (in_array($key, ['enum', 'type', 'required'], true)) {
            $this->checkUnique($schema);
        }

        if ($key !== 'enum') {
            $type = in_array($key, ['type', 'required'], true) ? 'string' : 'object';
            $this->checkContainerTypes($schema, $type);
        }
    }

    /**
     * @param array<mixed> $schema
     */
    protected function checkUnique(array $schema): void
    {
        if (!$this->comparer->uniqueArray($schema)) {
            $error = $this->formatError('unique', 'duplicates');
            throw new \RuntimeException($error);
        }
    }
}
