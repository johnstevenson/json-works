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

use JohnStevenson\JsonWorks\Helpers\Tokenizer;

class Target
{
    const TYPE_VALUE = 0;
    const TYPE_OBJECT = 1;
    const TYPE_ARRAY = 2;

    /**
    * @var bool
    */
    public $found;

    /**
    * @var integer
    */
    public $type;

    /**
    * @var string|integer
    */
    public $key;

    /**
    * @var array
    */
    public $tokens;

    /**
    * @var mixed
    */
    public $parent;

    /**
    * @var string
    */
    public $childKey;

    public function __construct($path)
    {
        $tokenizer = new Tokenizer();

        $this->tokens = $tokenizer->decode($path);
        $this->found = empty($this->tokens);

        $this->type = self::TYPE_VALUE;
        $this->key = '';
        $this->childKey = '';
    }

    public function setArray($index)
    {
        $this->type = self::TYPE_ARRAY;
        $this->key = (int) $index;
    }

    public function setObject($key)
    {
        $this->type = self::TYPE_OBJECT;
        $this->key = $key;
    }
}
