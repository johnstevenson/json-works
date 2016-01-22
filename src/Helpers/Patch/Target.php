<?php
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Helpers\Patch;

class Target
{
    const TYPE_SCALAR = 0;
    const TYPE_PROPKEY = 1;
    const TYPE_ARRAYKEY = 2;
    const TYPE_PUSH = 3;

    /**
    * @var bool
    */
    public $found;

    /**
    * @var integer
    */
    public $type;

    /**
    * @var integer
    */
    public $arrayKey;

    /**
    * @var string
    */
    public $propKey;

    /**
    * @var array
    */
    public $tokens;

    /**
    * @var string
    */
    public $lastKey;

    /**
    * @var mixed
    */
    public $parent;

    public function __construct()
    {
        $this->found = false;
        $this->type = self::TYPE_SCALAR;
        $this->arrayKey = 0;
        $this->propKey = '';
        $this->tokens = [];
        $this->lastKey = '';
    }

    public function setTokens($tokens)
    {
        $this->tokens = $tokens;
        $this->found = empty($this->tokens);
    }
}
