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

use JohnStevenson\JsonWorks\Schema\Constraints\Manager;

abstract class BaseConstraint
{
    /**
    * @var Manager
    */
    protected $manager;

    /**
    * @var integer
    */
    protected $errorCount = 0;

    abstract protected function run($data, $schema, $key = null);

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function check($data, $schema, $key = null)
    {
        $this->errorCount = 0;
        $this->run($data, $schema, $key);

        return $this->errorCount === 0;
    }

    protected function addError($error)
    {
        $this->manager->addError($error);
        $this->errorCount += 1;
    }

    public function get($schema, $key, $default = null)
    {
        return $this->manager->get($schema, $key, $default);
    }

    public function getValue($schema, $key, &$value, &$type)
    {
        return $this->manager->getValue($schema, $key, $value, $type);
    }

    protected function throwSchemaError($expected, $value)
    {
        $this->manager->throwSchemaError($expected, $value);
    }

    protected function validateChild($data, $schema, $key = null)
    {
        return $this->manager->validateChild($data, $schema, $key);
    }

    protected function match($regex, $string)
    {
        return preg_match('{'.$regex.'}', $string, $match);
    }
}
