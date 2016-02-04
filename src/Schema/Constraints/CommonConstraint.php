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

class CommonConstraint extends BaseConstraint
{
    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
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

        foreach ($schema as $key => $subSchema) {

            if (isset($common[$key])) {
                $this->getValue($schema, $key, $subSchema, $type, $common[$key]);
                //$name = preg_match('/(?:Of|not)$/', $key) ? 'of' : $key;
                $this->manager->check($key, [$data, $subSchema, $key]);
            }
        }
    }
}
