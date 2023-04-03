<?php declare(strict_types=1);

/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Schema\Constraint;

use JohnStevenson\JsonWorks\Helpers\Utils;
use JohnStevenson\JsonWorks\Schema\JsonTypes;

class TypeConstraint extends BaseConstraint
{
    protected jsonTypes $jsonTypes;

    /** @var array<string> */
    protected array $types;

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

    /**
    * @param mixed $data
    * @param array<mixed> $schema
    */
    public function validate($data, array $schema): void
    {
        if (Utils::arrayIsEmpty($schema)) {
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

    /**
    * @param array<mixed> $schema
    */
    protected function checkSchema(array $schema): void
    {
        $unknown = array_diff($schema, $this->types);

        if (Utils::arrayNotEmpty($unknown)) {
            $error = $this->formatError(implode('|', $this->types), implode('', $unknown));
            throw new \RuntimeException($error);
        }
    }
}
