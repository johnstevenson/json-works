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

use JohnStevenson\JsonWorks\Helpers\Tokenizer;
use JohnStevenson\JsonWorks\Helpers\Utils;
use JohnStevenson\JsonWorks\Schema\Constraint\Manager;
use JohnStevenson\JsonWorks\Schema\ValidationException;

abstract class BaseConstraint implements ConstraintInterface
{
    protected Manager $manager;
    protected Tokenizer $tokenizer;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        $this->tokenizer = new Tokenizer();
    }

    protected function addError(string $error): void
    {
        $path = $this->tokenizer->encode($this->manager->dataPath);

        if (Utils::stringIsEmpty($path)) {
            $path = '#';
        }

        $this->manager->errors[] = sprintf("Property: '%s'. Error: %s", $path, $error);

        if ($this->manager->stopOnError) {
            throw new ValidationException();
        }
    }

    /**
     * @param mixed $value Set by method
     * @param array<string>|string|null $required
     */
    public function getValue(stdClass $schema, string $key, &$value, $required = null): bool
    {
        return $this->manager->getValue($schema, $key, $value, $required);
    }

    protected function formatError(string $expected, string $value): string
    {
        return $this->manager->dataChecker->formatError($expected, $value);
    }

    protected function matchPattern(string $pattern, string $string): bool
    {
        $regex = sprintf('#%s#', str_replace('#', '\\#', $pattern));

        // suppress any warnings
        set_error_handler(static function (int $code, string $msg): bool { return true; });

        try {
            $result = Utils::isMatch($regex, $string);
            restore_error_handler();
        } catch (\Composer\Pcre\PcreException $e) {
            restore_error_handler();
            $error = $this->formatError('valid regex', $pattern);
            throw new \RuntimeException($error);
        }

        return $result;
    }
}
