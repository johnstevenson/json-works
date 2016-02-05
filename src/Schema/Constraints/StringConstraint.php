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

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->maxMin = new MaxMinConstraint($manager);
    }

    public function validate($data, $schema)
    {
        // maxLength
        $this->maxMin->validate($data, $schema, 'maxLength');

        // minLength
        $this->maxMin->validate($data, $schema, 'minLength');

        // pattern
        if ($this->getValue($schema, 'pattern', $pattern, $type, 'string')) {
            if (!$this->match($pattern, $data)) {
                $this->addError(sprintf('does not match pattern: %s', $pattern));
            }
        }

        // format
        if ($this->getValue($schema, 'format', $format, $type, 'string')) {
            $this->validateFormat($data, $format);
        }
    }

    protected function validateFormat($data, $format)
    {
        if ($format === 'date-time') {
            $this->checkDateTime($data);
        } elseif (in_array($format, ['email', 'hostname', 'ipv4', 'ipv6', 'uri'])) {
            $method = 'check' . ucfirst(preg_replace('/v[46]$/', '', $format));
            $this->$method($data, $format);
        } else {
            $this->addError('Unknown format, '.$data);
        }
    }

    protected function checkDateTime($data)
    {
        $p = '/^\d{4}-\d{2}-\d{2}[T| ]\d{2}:\d{2}:\d{2}(\.\d{1})?(Z|[\+|-]\d{2}:\d{2})?$/i';

        if (!preg_match($p, $data, $match) || false === strtotime($data)) {
            $this->setFormatError($data, 'date-time');
        }
    }

    protected function checkEmail($data, $format)
    {
        $this->filter($data, $format, FILTER_VALIDATE_EMAIL);
    }

    protected function checkHostname($data, $format)
    {
        if (!preg_match('/^[_a-z]+\.([_a-z]+\.?)+$/i', $data) || strlen($data) > 255) {
            $this->setFormatError($data, $format);
        }
    }

    protected function checkIp($data, $format)
    {
        $flags = $format === 'ipv4' ? FILTER_FLAG_IPV4 : FILTER_FLAG_IPV6;
        $this->filter($data, $format, FILTER_VALIDATE_IP, $flags);
    }

    protected function checkUri($data, $format)
    {
        $this->filter($data, $format, FILTER_VALIDATE_URL);
    }

    protected function filter($data, $format, $filter, $flags = 0)
    {
        $flags |= FILTER_NULL_ON_FAILURE;

        if (null === filter_var($data, $filter, $flags)) {
            $this->setFormatError($data, $format);
        }
    }

    protected function setFormatError($data, $format)
    {
        $error = sprintf("Invalid %s '%s'", $format, $data);
        $this->addError($data);
    }
}
