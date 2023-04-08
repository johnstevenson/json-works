<?php declare(strict_types=1);

/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Helpers\Format;

/**
 * A class to return an unreferenced copy of the data, with an optional callback
 */
class Copier extends BaseFormat
{
    /**
     * Returns an unreferenced copy of the data
     *
     * @internal
     * @param object|array<mixed>|mixed $data
     * @return object|array<mixed>|mixed
     */
    public function run($data)
    {
        $isObject = null;

        if ($this->isContainer($data, $asObject)) {
            // for phpstan
            if (is_object($data) || is_array($data)) {
                return $this->copyContainer($data, $asObject);
            }
        }

        return $data;
    }

    /**
     * Recursively copies an object or array
     *
     * @param object|array<mixed> $data The data to copy
     * @return object|array<mixed> An unreferenced copy
     */
    protected function copyContainer($data, bool $asObject)
    {
        $result = [];

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        foreach ($data as $key => $value) {
            $result[$key] = $this->run($value);
        }

        return $this->formatContainer($result, $asObject);
    }
}
