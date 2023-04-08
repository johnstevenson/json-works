<?php declare(strict_types=1);

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
use JohnStevenson\JsonWorks\Helpers\Utils;
use JohnStevenson\JsonWorks\Tokenizer;

/**
* A class for holding various properties when searching for or building data
*/
class Target
{
    const TYPE_VALUE = 0;
    const TYPE_OBJECT = 1;
    const TYPE_ARRAY = 2;

    /** @var string|integer */
    public $key = '';

    /** @var array<string> */
    public $tokens = [];

    /** @var mixed */
    public $element;

    public string $foundPath = '';

    /** @var mixed */
    public $parent;

    public bool $invalid = false;
    public int $type = self::TYPE_VALUE;
    public string $path = '';
    public string $childKey = '';
    public string $error = '';
    public Tokenizer $tokenizer;

    /**
    * Constructor
    *
    */
    public function __construct(string $path, string &$error)
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
    * @param string|integer $index
    */
    public function setArray($index): void
    {
        $this->type = self::TYPE_ARRAY;
        $this->key = (int) $index;
    }

    /**
    * Sets type and key for an object
    */
    public function setObject(string $key): void
    {
        $this->type = self::TYPE_OBJECT;
        $this->key = $key;
    }

    /**
    * Sets or clears an error message
    */
    public function setError(?string $code): void
    {
        $this->error = '';

        if ($code !== null) {
            $error = new Error();
            $this->error = $error->get($code, $this->path);
            $this->invalid = $code === Error::ERR_PATH_KEY;
        }
    }

    /**
    * Add a token to the found path
    */
    public function setFoundPath(string $token): void
    {
        $this->foundPath = $this->tokenizer->add($this->foundPath, $token);
    }

    /**
    * Sets element and error if not already set
    *
    * @param mixed $element
    */
    public function setResult(bool $found, &$element): void
    {
        $this->element =& $element;

        if (!$found && Utils::stringIsEmpty($this->error)) {
            $this->setError(Error::ERR_NOT_FOUND);
        }
    }
}
