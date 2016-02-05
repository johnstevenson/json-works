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

class FormatConstraint extends BaseConstraint
{
    /**
    * The main method
    *
    * @param string $data
    * @param string $format
    */
    public function validate($data, $format)
    {
        if (!$this->checkKnownFormat($data, $format)) {
            $error = sprintf("Unknown format '%s'", $format);
            $this->addError($error);
        }
    }

    protected function checkKnownFormat($data, $format)
    {
        if ($format === 'date-time') {
            $method = 'checkDateTime';
        } else {
            $method = 'check' . ucfirst(preg_replace('/v[46]$/', '', $format));
        }

        if ($result = method_exists($this, $method)) {
            $this->$method($data, $format);
        }

        return $result;
    }

    protected function checkDateTime($data, $format)
    {
        $regex = '/^\d{4}-\d{2}-\d{2}[T| ]\d{2}:\d{2}:\d{2}(\.\d{1})?(Z|[\+|-]\d{2}:\d{2})?$/i';

        if (!preg_match($regex, $data) || false === strtotime($data)) {
            $this->setError($data, $format);
        }
    }

    protected function checkEmail($data, $format)
    {
        $this->filter($data, $format, FILTER_VALIDATE_EMAIL);
    }

    protected function checkHostname($data, $format)
    {
        $regex = '/^[_a-z]+\.([_a-z]+\.?)+$/i';

        if (!preg_match($regex, $data) || strlen($data) > 255) {
            $this->setError($data, $format);
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
            $this->setError($data, $format);
        }
    }

    protected function setError($data, $format)
    {
        $error = sprintf("Invalid %s '%s'", $format, $data);
        $this->addError($error);
    }
}
