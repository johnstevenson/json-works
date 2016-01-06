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

use JohnStevenson\JsonWorks\Helpers\Format\CopyFormatter;
use JohnStevenson\JsonWorks\Helpers\Format\OrderFormatter;
use JohnStevenson\JsonWorks\Helpers\Format\PruneFormatter;

/**
* A class for manipulating array, object or json data
*/
class FormatManager
{
    /**
    * @var CopyFormatter
    */
    protected $copier;

    protected $orderer;

    /**
    * @var PruneFormatter
    */
    protected $pruner;

    /**
    * Returns an unreferenced copy of the data
    *
    * @param mixed $data
    * @param callable|null $callback Optional callback function
    * @return mixed
    */
    public function copy($data, $callback = null)
    {
        if (!$this->copier) {
            $this->copier = new CopyFormatter();
        }

        return $this->copier->run($data, $callback);
    }

    public function order($data, $schema)
    {
        if (!$this->orderer) {
            $this->orderer = new OrderFormatter();
        }

        return $this->orderer->run($data, $schema);
    }

    public function prune($data)
    {
        if (!$this->pruner) {
            $this->pruner = new PruneFormatter();
        }

        return $this->pruner->run($data);
    }
}
