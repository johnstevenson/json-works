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

abstract class Matcher extends BaseConstraint
{
    protected function run($data, $schema, $key = null)
    {
        $this->getDetails($type, $matchFirst);
        $schemas = $this->checkSchema($schema, $type);
        $matches = 0;

        foreach ($schemas as $subSchema) {

            if ($this->validateChild($data, $subSchema)) {
                ++$matches;

                if ($matchFirst) {
                    break;
                }
            }
        }

        if (!$this->getResult($matches, count($schemas), $error)) {
            $this->addError($error);
        }
    }

    protected function checkSchema($schema, $type)
    {
        if ($type !== gettype($schema)) {
            $this->throwSchemaError($type, gettype($schema));
        }

        return $type === 'object' ? [$schema] : $schema ;
    }
}
