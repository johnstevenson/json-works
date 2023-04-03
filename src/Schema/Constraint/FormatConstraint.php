<?php declare(strict_types=1);

/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Schema\Constraint;

class FormatConstraint extends BaseConstraint
{
    public function validate(string $data, string $format): void
    {
        if (!$this->checkKnownFormat($data, $format)) {
            $error = sprintf("Unknown format '%s'", $format);
            $this->addError($error);
        }
    }

    protected function checkKnownFormat(string $data, string $format): bool
    {
        if ($format === 'date-time') {
            $this->checkDateTime($data, $format);
            return true;
        }

        if ($format === 'email') {
            $this->checkEmail($data, $format);
            return true;
        }

        if ($format === 'hostname') {
            $this->checkHostname($data, $format);
            return true;
        }

        if ($format === 'ipv4' || $format === 'ipv6') {
            $this->checkIp($data, $format);
            return true;
        }

        if ($format === 'uri') {
            $this->checkUri($data, $format);
            return true;
        }

        return false;
    }

    protected function checkDateTime(string $data, string $format): void
    {
        $regex = '/^\d{4}-\d{2}-\d{2}[T| ]\d{2}:\d{2}:\d{2}(\.\d{1})?(Z|[\+|-]\d{2}:\d{2})?$/i';

        if (!preg_match($regex, $data) || false === strtotime($data)) {
            $this->setError($data, $format);
        }
    }

    protected function checkEmail(string $data, string $format): void
    {
        $this->filter($data, $format, FILTER_VALIDATE_EMAIL);
    }

    protected function checkHostname(string $data, string $format): void
    {
        $regex = '/^[_a-z]+\.([_a-z]+\.?)+$/i';

        if (!preg_match($regex, $data) || strlen($data) > 255) {
            $this->setError($data, $format);
        }
    }

    protected function checkIp(string $data, string $format): void
    {
        $flags = $format === 'ipv4' ? FILTER_FLAG_IPV4 : FILTER_FLAG_IPV6;
        $this->filter($data, $format, FILTER_VALIDATE_IP, $flags);
    }

    protected function checkUri(string $data, string $format): void
    {
        $this->filter($data, $format, FILTER_VALIDATE_URL);
    }

    protected function filter(string $data, string $format, int $filter, int $flags = 0): void
    {
        $flags |= FILTER_NULL_ON_FAILURE;

        if (null === filter_var($data, $filter, $flags)) {
            $this->setError($data, $format);
        }
    }

    protected function setError(string $data, string $format): void
    {
        $error = sprintf("Invalid %s '%s'", $format, $data);
        $this->addError($error);
    }
}
