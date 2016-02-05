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
    /**
    * @var JsonTypes
    */
    protected $jsonTypes;

    /**
    * @var array
    */
    protected $types;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->jsonTypes = new JsonTypes();

        $this->types = [
            'array',
            'boolean',
            'integer',
            'null',
            'number',
            'object',
            'string'
        ];
    }

    public function validate($data, array $schema)
    {
        if (empty($schema)) {
            return;
        }

        $this->checkSchema($schema);

        foreach ($schema as $type) {
            if ($this->jsonTypes->checkType($data, $type)) {
                return;
            }
        }

        $error = sprintf("value must be of type '%s'", implode('|', $schema));
        $this->addError($error);
    }

    protected function checkSchema(array $schema)
    {
        if ($unknown = array_diff($schema, $this->types)) {
            $error = $this->getSchemaError(implode('|', $this->types), implode('', $unknown));
            throw new \RuntimeException($error);
        }
    }
}
