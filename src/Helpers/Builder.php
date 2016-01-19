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

use JohnStevenson\JsonWorks\Helpers\Finder;
use JohnStevenson\JsonWorks\Helpers\Formatter;

/**
* A class for building json
*/
class Builder
{
    /**
    * @var Finder
    */
    protected $finder;

    /**
    * @var Formatter
    */
    protected $formatter;

    public function __construct()
    {
        $this->finder = new Finder();
        $this->formatter = new Formatter();
    }
}
