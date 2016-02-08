<?php
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Schema\Constraints;

use JohnStevenson\JsonWorks\Helpers\Tokenizer;
use JohnStevenson\JsonWorks\Schema\Constraints\Manager;
use JohnStevenson\JsonWorks\Schema\ValidationException;

abstract class BaseConstraint
{
    /**
    * @var Manager
    */
    protected $manager;

    /**
    * @var \JohnStevenson\JsonWorks\Helpers\Tokenizer
    */
    protected $tokenizer;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        $this->tokenizer = new Tokenizer;
    }

    protected function addError($error)
    {
        $path = $this->tokenizer->encode($this->manager->dataPath) ?: '#';
        $this->manager->errors[] = sprintf("Property: '%s'. Error: %s", $path, $error);

        if ($this->manager->stopOnError) {
            throw new ValidationException();
        }
    }

    public function getValue($schema, $key, &$value, $required = null)
    {
        return $this->manager->getValue($schema, $key, $value, $required);
    }

    protected function getSchemaError($expected, $value)
    {
        return $this->manager->getSchemaError($expected, $value);
    }

    protected function matchPattern($pattern, $string)
    {
        $regex = sprintf('#%s#', str_replace('#', '\\#', $pattern));

        // important to suppress any errors so we can throw an execption
        $result = @preg_match($regex, $string);

        if (false === $result) {
            $error = $this->getSchemaError('valid regex', $pattern);
            throw new \RuntimeException($error);
        }

        return (bool) preg_match($regex, $string);
    }
}
