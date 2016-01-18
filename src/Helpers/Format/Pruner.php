<?php
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
* A class to remove empty objects and arrays from data
*/
class Pruner extends BaseFormat
{
    /**
    * @var bool
    */
    protected $keep;

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

        if ($this->isContainer($data, $object)) {
            return $this->pruneContainer($data, $object);
        }

        return $data;
    }

    /**
    * Recursively removes empty objects and arrays from the container
    *
    * @param mixed $container The data container to prune
    * @param mixed $object Whether the result should be an object
    * @return object|array An unreferenced copy of the pruned container
    */
    protected function pruneContainer($container, $object)
    {
        $result = [];

        foreach ($container as $key => $value) {
            $value = $this->run($value);

            if ($this->keep) {
                $result[$key] = $value;
            }
        }

        $this->keep = !empty($result);

        return $this->formatContainer($result, $object);
    }
}
