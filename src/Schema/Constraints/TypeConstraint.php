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
    protected function run($data, $type, $key = null)
    {
        $types = (array) $type;
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
        return $this->manager->jsonTypes->checkType($data, $type);
    }
}
