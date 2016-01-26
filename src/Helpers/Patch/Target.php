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
    * Constructor
    *
    * @param string $path
    * @param string $error
    */
    public function __construct($path, &$error)
    {
        $this->path = $path;
        $this->error =& $error;

        $tokenizer = new Tokenizer();
        $this->tokens = $tokenizer->decode($this->path);

        if (in_array('', $this->tokens, true)) {
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

        if ($code) {
            $error = new Error();
            $this->error = $error->get($code, $this->path);
        }
    }

    /**
    * Sets element and error if not already set
    *
    * @api
    * @param bool $found If the element has been found
    */
    public function setResult($found, &$element)
    {
        $this->element =& $element;

        if (!$found && !$this->error) {
            $this->setError(Error::ERR_NOT_FOUND);
        }
    }
}
