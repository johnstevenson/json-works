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

class StringConstraint extends BaseConstraint
{
    protected $maxMin;
    protected $format;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->maxMin = new MaxMinConstraint($manager);
        $this->format = new FormatConstraint($manager);
    }

    public function validate($data, $schema)
    {
        // maxLength
        $this->maxMin->validate($data, $schema, 'maxLength');

        // minLength
        $this->maxMin->validate($data, $schema, 'minLength');

        // format
        $this->format->validate($data, $schema);

        // pattern
        $this->checkPattern($data, $schema);
    }

    protected function checkPattern($data, $schema)
    {
        if (!$this->getValue($schema, 'pattern', $pattern, $type, 'string')) {
            return;
        }

        if (!$this->matchPattern($pattern, $data)) {
            $this->addError(sprintf('does not match pattern: %s', $pattern));
        }
    }
}
