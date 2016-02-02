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

class AnyOfConstraint extends Matcher implements MatcherInterface
{
    public function getDetails(&$type, &$matchFirst)
    {
        $type = 'array';
        $matchFirst = true;
    }

    public function getResult($matches, $schemaCount, &$error)
    {
        $result = $matches !== 0;

        if (!$result) {
            $error = "does not match 'anyOf' schema requirements";
        }

        return $result;
    }
}
