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
    public function validate($data, $schema)
    {
        $errors = count($this->manager->errors);
        $this->run($data, $schema);

        return count($this->manager->errors) === $errors;
    }

    protected function run($data, $schema)
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


                $this->checkCommon($data, $subSchema, $key);
            }
        }
    }

    protected function checkCommon($data, $subSchema, $key)
    {
        $name = in_array($key, ['enum', 'type']) ? $key : 'of';
        $validator = $this->manager->factory($name);

        if ($name === 'of') {
            $validator->validate($data, $subSchema, $key);
        } else {
            $validator->validate($data, $subSchema);
        }
    }
}
