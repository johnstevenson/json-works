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

use \stdClass;

use JohnStevenson\JsonWorks\Helpers\Utils;
use JohnStevenson\JsonWorks\Schema\Comparer;
use JohnStevenson\JsonWorks\Schema\Constraint\Manager;

class EnumConstraint extends BaseConstraint implements ConstraintInterface
{
    protected Comparer $comparer;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->comparer = new Comparer();
    }

    /**
     * @param mixed $data
     * @param stdClass|array<mixed> $schema
     */
    public function validate($data, $schema, ?string $key = null): void
    {
        if (!is_array($schema)) {
            $error = Utils::getArgumentError('$schema', 'array', $schema);
            throw new \InvalidArgumentException($error);
        }

        foreach ($schema as $value) {
            if ($this->comparer->equals($value, $data)) {
                return;
            }
        }

        $error = sprintf("value not found in enum '%s'", json_encode($schema));
        $this->addError($error);
    }
}
