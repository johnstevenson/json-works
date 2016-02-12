<?php
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Helpers;

use stdClass;
use JohnStevenson\JsonWorks\Helpers\Format\Copier;
use JohnStevenson\JsonWorks\Helpers\Format\Orderer;
use JohnStevenson\JsonWorks\Helpers\Format\Pruner;

/**
* A class for manipulating array, object or json data
*/
class Formatter
{
    /**
    * @var Format\Copier
    */
    protected $copier;

    /**
    * @var Format\Orderer
    */
    protected $orderer;

    /**
    * @var Format\Pruner
    */
    protected $pruner;

    /**
    * Returns an unreferenced copy of the data
    *
    * @api
    * @param mixed $data
    * @return mixed
    */
    public function copy($data)
    {
        if (!$this->copier) {
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
        if (!$this->orderer) {
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
        if (!$this->pruner) {
            $this->pruner = new Pruner();
        }

        return $this->pruner->run($data);
    }

    /**
    * Returns json, with any _empty_ properties renamed
    *
    * @param mixed $data
    * @param integer $options
    * @return string
    */
    public function toJson($data, $options)
    {
        return str_replace('"_empty_":', '"":', json_encode($data, $options));
    }
}
