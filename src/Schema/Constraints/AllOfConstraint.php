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

class AllOfConstraint extends Matcher
{
    protected function getDetails(&$type, &$matchFirst)
    {
        $type = 'array';
        $matchFirst = false;
    }

    protected function getResult($matches, $schemaCount, &$error)
    {
        $result = $matches === $schemaCount;

        if (!$result) {
            $error = "does not match 'allOf' schema requirements";
        }

        return $result;
    }
}
