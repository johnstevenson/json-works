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

use JohnStevenson\JsonWorks\Schema\Constraints\Comparer;
use JohnStevenson\JsonWorks\Schema\Constraints\Manager;

class EnumConstraint extends BaseConstraint
{
    protected $comparer;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->comparer = new Comparer();
    }

    protected function run($data, $enum, $key = null)
    {
        if (!$this->checkEnum($data, $enum)) {
            $error = sprintf("'value not found in enum '%s'", json_encode($enum));
            $this->addError($error);
        }
    }

    protected function checkEnum($data, $enum)
    {
        $result = false;

        foreach ((array) $enum as $value) {
            if ($result = $this->comparer->equals($value, $data)) {
                break;
            }
        }

        return $result;
    }
}
