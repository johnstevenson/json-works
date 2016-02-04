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

class InstanceConstraint extends BaseConstraint
{
    /**
    * @var JsonTypes
    */
    protected $jsonTypes;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->jsonTypes = new JsonTypes();
    }

    protected function run($data, $schema, $key = null)
    {
        if ($name = $this->getInstanceName($data)) {
            $this->manager->check($name, [$data, $schema]);
        }
    }

    protected function getInstanceName($data)
    {
        $result = $this->jsonTypes->getGeneric($data);

        if (in_array($result, ['boolean', 'null'])) {
            $result = '';
        }

        return $result;
    }
}
