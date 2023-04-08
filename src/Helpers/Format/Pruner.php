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

use JohnStevenson\JsonWorks\Helpers\Utils;

/**
* A class to remove empty objects and arrays from data
*/
class Pruner extends BaseFormat
{
    protected bool $keep;

    /**
    * Removes empty objects and arrays from the data
    *
    * @internal
    * @param mixed $data The data to prune
    * @return mixed An unreferenced copy of the pruned data
    */
    public function run($data)
    {
        $this->keep = true;

        if ($this->isContainer($data, $asObject)) {
            $container = is_object($data) ? get_object_vars($data) : (array) $data;

            return $this->pruneContainer($container, $asObject);
        }

        return $data;
    }

    /**
    * Recursively removes empty objects and arrays from the container
    *
    * @param array<mixed> $container The data container to prune
    * @return object|array<mixed> An unreferenced copy of the pruned container
    */
    protected function pruneContainer(array $container, bool $asObject)
    {
        $result = [];

        foreach ($container as $key => $value) {
            $value = $this->run($value);

            if ($this->keep) {
                $result[$key] = $value;
            }
        }

        $this->keep = Utils::arrayNotEmpty($result);

        return $this->formatContainer($result, $asObject);
    }
}
