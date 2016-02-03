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

class TypeConstraint extends BaseConstraint
{
    protected $comparer;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->comparer = new Comparer();
    }

    protected function run($data, $schema, $key = null)
    {
        $types = (array) $schema;
        $result = false;

        foreach ($types as $type) {
            if ($result = $this->checkType($data, $type)) {
                break;
            }
        }

        if (!$result) {
            $error = sprintf("value must be of type '%s'", implode(', ', $types));
            $this->addError($error);
        }
    }

    protected function checkType($data, $type)
    {
        return $this->comparer->checkType($data, $type);
    }

    protected function checkSchema($schema)
    {
        $result = (array) $schema;

        if (!$this->comparer->uniqueArrayOfString($schema)) {
            $error = $this->getSchemaError('strings', 'mixed');
            throw new \RuntimeException($error);
        }

        return $result;
    }
}
