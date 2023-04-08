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

use JohnStevenson\JsonWorks\Helpers\Format\Copier;
use JohnStevenson\JsonWorks\Helpers\Format\Orderer;
use JohnStevenson\JsonWorks\Helpers\Format\Pruner;

/**
* A class for manipulating array, object or json data
*/
class Formatter
{
    protected ?Copier $copier = null;
    protected ?Orderer $orderer = null;
    protected ?Pruner $pruner = null;

    /**
    * Returns an unreferenced copy of the data
    *
    * @api
    * @param mixed $data
    * @return mixed
    */
    public function copy($data)
    {
        if ($this->copier === null) {
            $this->copier = new Copier();
        }

        return $this->copier->run($data);
    }

    /**
    * Reorders object properties using the schema order
    *
    * @api
    * @param mixed $data
    * @param stdClass|null $schema
    * @return mixed An unreferenced copy of the ordered data
    */
    public function order($data, $schema)
    {
        if ($this->orderer === null) {
            $this->orderer = new Orderer();
        }

        $data = $this->copy($data);

        return $this->orderer->run($data, $schema);
    }

    /**
    * Removes empty objects and arrays from the data
    *
    * @api
    * @param mixed $data
    * @return mixed An unreferenced copy of the pruned data
    */
    public function prune($data)
    {
        if ($this->pruner === null) {
            $this->pruner = new Pruner();
        }

        return $this->pruner->run($data);
    }

    /**
     * @param mixed $data
     */
    public function toJson($data, int $options): string
    {
        return Utils::jsonEncode($data, $options);
    }
}
