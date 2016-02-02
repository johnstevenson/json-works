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

class NumberConstraint extends BaseConstraint
{
    protected function run($data, $schema, $key = null)
    {
        # maximum
        if (isset($schema->maximum)) {
            $max = $schema->maximum;

            if ($exclusive = $this->get($schema, 'exclusiveMaximum', false)) {
                $valid = $data < $max;
            } else {
                $valid = $data <= $max;
            }

            if (!$valid) {
                $error = 'value must be less than ';
                $error .= $exclusive ? 'or equal to ' : '';
                $this->addError($error.$max);
            }
        }

        # minimum
        if (isset($schema->minimum)) {
            $min = $schema->minimum;

            if ($exclusive = $this->get($schema, 'exclusiveMinimum', false)) {
                $valid = $data > $min;
            } else {
                $valid = $data >= $min;
            }

            if (!$valid) {
                $error = 'value must be greater than ';
                $error .= $exclusive ? '' : 'or equal to ';
                $this->addError($error.$min);
            }
        }
    }
}
