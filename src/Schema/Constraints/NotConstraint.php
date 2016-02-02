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

class NotConstraint extends Matcher
{
    protected function getDetails(&$type, &$matchFirst)
    {
        $type = 'object';
        $matchFirst = true;
    }

    protected function getResult($matches, $schemaCount, &$error)
    {
        $result = $matches === 0;

        if (!$result) {
            $error = 'must not validate against this schema';
        }

        return $result;
    }
}
