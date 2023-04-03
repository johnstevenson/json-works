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
use JohnStevenson\JsonWorks\Schema\JsonTypes;
use JohnStevenson\JsonWorks\Schema\Constraint\Manager;

class SpecificConstraint extends BaseConstraint
{
    protected JsonTypes $jsonTypes;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->jsonTypes = new JsonTypes();
    }

    /**
    * @param mixed $data
    */
    public function validate($data, stdClass $schema): void
    {
        $name = $this->getInstanceName($data);
        if (Utils::stringNotEmpty($name)) {
            $validator = $this->manager->factory($name);
            $validator->validate($data, $schema);
        }
    }

    /**
    * @param mixed $data
    */
    protected function getInstanceName($data): string
    {
        $result = $this->jsonTypes->getGeneric($data);

        if (in_array($result, ['boolean', 'null'], true)) {
            $result = '';
        }

        return $result;
    }
}
