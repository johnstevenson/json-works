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
    protected function run($data, $schema, $key = null)
    {
        # maxLength
        if (isset($schema->maxLength)) {
            if (strlen($data) > $schema->maxLength) {
                $this->addError(sprintf('has too many characters, maximum (%d)', $schema->maxLength));
            }
        }

        # minLength
        if (isset($schema->minLength)) {
            if (strlen($data) < $schema->minLength) {
                $this->addError(sprintf('has too few characters, minimum (%d)', $schema->minLength));
            }
        }

        # pattern
        if (isset($schema->pattern)) {
            if (!$this->match($schema->pattern, $data)) {
                $this->addError(sprintf('does not match pattern: %s', $schema->pattern));
            }
        }

        # format
        if (isset($schema->format)) {
            $this->validateFormat($data, $schema->format);
        }
    }

    protected function validateFormat($data, $format)
    {
        switch ($format) {

            case 'date-time':
                $p = '/^\d{4}-\d{2}-\d{2}[T| ]\d{2}:\d{2}:\d{2}(\.\d{1})?(Z|[\+|-]\d{2}:\d{2})?$/i';
                if (!preg_match($p, $data, $match) || false === strtotime($data)) {
                    $this->addError('Invalid date-time, '.json_encode($data));
                }
                break;

            case 'email':
                if (null === filter_var($data, FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE)) {
                    $this->addError('Invalid email, '.json_encode($data));
                }
                break;

            case 'hostname':
                if (!preg_match('/^[_a-z]+\.([_a-z]+\.?)+$/i', $data)) {
                    $this->addError('Invalid hostname, '.json_encode($data));
                }
                break;

            case 'ipv4':
                if (null === filter_var($data, FILTER_VALIDATE_IP, FILTER_NULL_ON_FAILURE | FILTER_FLAG_IPV4)) {
                    $this->addError('Invalid IPv4 address, '.json_encode($data));
                }
                break;

            case 'ipv6':
                if (null === filter_var($data, FILTER_VALIDATE_IP, FILTER_NULL_ON_FAILURE | FILTER_FLAG_IPV6)) {
                    $this->addError('Invalid IPv6 address, '.json_encode($data));
                }
                break;

            case 'uri':
                if (null === filter_var($data, FILTER_VALIDATE_URL, FILTER_NULL_ON_FAILURE)) {
                    $this->addError('Invalid uri, '.json_encode($data));
                }
                break;

            default:
                $this->addError('Unknown format, '.json_encode($data));
        }
    }
}
