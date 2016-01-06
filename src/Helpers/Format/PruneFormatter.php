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
* A class to remove unused elements and return an unreferenced copy of data
*/
class PruneFormatter extends BaseFormatter
{
    public function run($data)
    {
        $props = 0;

        return $this->prune($data, $props);
    }

    protected function prune($data, &$props)
    {
        if ($this->isContainer($data, $isObject)) {
            return $this->pruneContainer($data, $isObject, $props);
        }

        ++$props;

        return $data;
    }

    protected function pruneContainer($data, $isObject, &$props)
    {
        $result = array();
        $currentProps = $props;

        foreach ($data as $key => $value) {
            $isObject = $this->isObject($key, $isObject);
            $value = $this->prune($value, $props);

            if ($props > $currentProps) {
                $result[$key] = $value;
            }
            $props = $currentProps;
        }

        $props = count($result);

        return $this->formatContainer($result, $isObject);
    }
}
