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

use JohnStevenson\JsonWorks\Helpers\Error;
use JohnStevenson\JsonWorks\Helpers\Tokenizer;

/**
* A class for holding various properties when searching for or building data
*/
class Target
{
    const TYPE_VALUE = 0;
    const TYPE_OBJECT = 1;
    const TYPE_ARRAY = 2;

    /**
    * @var bool
    */
    public $invalid = false;

    /**
    * @var integer
    */
    public $type = self::TYPE_VALUE;

    /**
    * @var string|integer
    */
    public $key = '';

    /**
    * @var string
    */
    public $path = '';

    /**
    * @var array
    */
    public $tokens = [];

    /**
    * @var mixed
    */
    public $element;

    /**
    * @var string
    */
    public $foundPath = '';

    /**
    * @var mixed
    */
    public $parent;

    /**
    * @var string
    */
    public $childKey = '';

    /**
    * @var string
    */
    public $error = '';

    /**
    * @var \JohnStevenson\JsonWorks\Helpers\Tokenizer
    */
    public $tokenizer;

    /**
    * Constructor
    *
    * @param string $path
    * @param string $error
    */
    public function __construct($path, &$error)
    {
        $this->path = $path;
        $this->error =& $error;

        $this->tokenizer = new Tokenizer();

        if (!$this->tokenizer->decode($this->path, $this->tokens)) {
            $this->invalid = true;
            $this->setError(Error::ERR_PATH_KEY);
        }
    }

    /**
    * Sets type and key for an array
    *
    * @param string|number $index
    */
    public function setArray($index)
    {
        $this->type = self::TYPE_ARRAY;
        $this->key = (int) $index;
    }

    /**
    * Sets type and key for an object
    *
    * @param string $key
    */
    public function setObject($key)
    {
        $this->type = self::TYPE_OBJECT;
        $this->key = $key;
    }

    /**
    * Sets or clears an error message
    *
    * @api
    * @param string|null $code
    */
    public function setError($code)
    {
        $this->error = '';

        if (!empty($code)) {
            $error = new Error();
            $this->error = $error->get($code, $this->path);
            $this->invalid = $code === Error::ERR_PATH_KEY;
        }
    }

    /**
    * Add a token to the found path
    *
    * @api
    * @param string $token
    */
    public function setFoundPath($token)
    {
        $this->foundPath = $this->tokenizer->add($this->foundPath, $token);
    }

    /**
    * Sets element and error if not already set
    *
    * @api
    * @param bool $found If the element has been found
    * @param mixed $element
    */
    public function setResult($found, &$element)
    {
        $this->element =& $element;

        if (!$found && !$this->error) {
            $this->setError(Error::ERR_NOT_FOUND);
        }
    }
}
