<?php declare(strict_types=1);
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Helpers;

use \stdClass;

use Composer\Pcre\Preg;

class Utils
{
    public static function stringIsEmpty(string $value): bool
    {
        return strlen($value) === 0;
    }

    public static function stringNotEmpty(string $value): bool
    {
        return strlen($value) !== 0;
    }

    public static function stringIsJson(string $value): bool
    {
        $value = trim($value);
        $objectRegex = '#^\\{(?:.*)\\}$#s';
        $arrayRegex = '#^\\[(?:.*)\\]$#s';

        return (Preg::isMatch($objectRegex, $value)
            || Preg::isMatch($arrayRegex, $value));
    }

    /**
     * @param non-empty-string   $pattern
     * @param array<string|null> $matches Set by method
     * @param-out array<int|string, string|null> $matches
     */
    public static function isMatch(string $pattern, string $value, ?array &$matches = null): bool
    {
        return Preg::isMatch($pattern, $value, $matches);
    }

    /**
     * @param array<mixed> $value
     */
    public static function arrayIsEmpty(array $value): bool
    {
        return count($value) === 0;
    }

    /**
     * @param array<mixed> $value
     */
    public static function arrayNotEmpty(array $value): bool
    {
        return count($value) > 0;
    }

    /**
     * @param mixed $data
     */
    public static function jsonEncode($data, int $options = 0): string
    {
        $options = ($options & ~JSON_THROW_ON_ERROR);
        $result = json_encode($data, $options);

        if ($result === false) {
            throw new \RuntimeException(json_last_error_msg());
        }

        return $result;
    }
}
