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

use \stdClass;

class ItemsConstraint extends BaseConstraint
{
    /**
     * @param mixed $data
     */
    public function validate($data, stdClass $schema): void
    {
        list($items, $additional) = $this->getItemValues($schema);

        if ($items instanceof stdClass) {
            $this->validateObjectItems($data, $items);
            return;
        }

        if (is_array($items)) {
            $this->checkArrayItems($data, $items, $additional);
            $this->validateArrayItems($data, $items, $additional);
        }
    }

    /**
     * Returns items and additionalItems values
     *
     * @return array{0: array<mixed>|object, 1: object|boolean|null}
     */
    protected function getItemValues(stdClass $schema): array
    {
        $items = null;
        $this->getValue($schema, 'items', $items, ['array', 'object']);

        if ($items === null) {
            $items = [];
        }

        $additional = null;
        $this->getValue($schema, 'additionalItems', $additional, ['boolean', 'object']);

        return [$items, $additional];
    }

    /**
     * @param mixed $data
     */
    protected function validateObjectItems($data, stdClass $schema): void
    {
        $key = 0;

        foreach ($data as $value) {
            $this->manager->validate($value, $schema, strval($key));
            ++$key;
        }
    }

    /**
     * @param mixed $data
     * @param array<mixed> $items
     * @param object|boolean|null $additional
     */
    protected function checkArrayItems($data, array $items, $additional): void
    {
        if (false === $additional) {
            if (count($data) > count($items)) {
                $this->addError('contains more elements than are allowed');
            }
        }
    }

    /**
     * @param mixed $data
     * @param array<mixed> $items
     * @param object|boolean|null $additional
     */
    protected function validateArrayItems($data, array $items, $additional): void
    {
        $key = 0;
        $itemsCount = count($items);

        foreach ($data as $value) {

            if ($key < $itemsCount) {
                $this->manager->validate($value, $items[$key], strval($key));
            } else {

                if ($additional instanceof stdClass) {
                    $this->validateObjectItems(array_slice($data, $key), $additional);
                }
                break;
            }

            ++$key;
        }
    }
}
