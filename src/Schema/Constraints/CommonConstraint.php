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

class CommonConstraint extends BaseConstraint
{
    protected $comparer;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->comparer = new Comparer();
    }

    protected function run($data, $schema, $key = null)
    {
        $common = [
            'enum' => 'array',
            'type' => ['array', 'string'],
            'allOf' => 'array',
            'anyOf' => 'array',
            'oneOf' => 'array',
            'not' => 'object'
        ];

        foreach ($common as $key => $required) {

            if ($this->getValue($schema, $key, $subSchema, $type, $required)) {
                $name = preg_match('/(?:Of|not)$/', $key) ? 'of' : $key;
                $this->manager->check($name, [$data, $subSchema, $key]);
            }
        }
    }
}
