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
    protected $jsonTypes;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->jsonTypes = new JsonTypes();
    }

    public function validate($data, $schema)
    {
        $types = (array) $schema;
        $result = false;

        foreach ($types as $type) {
            if ($result = $this->jsonTypes->checkType($data, $type)) {
                break;
            }
        }

        if (!$result) {
            $error = sprintf("value must be of type '%s'", implode(', ', $types));
            $this->addError($error);
        }
    }
}
