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

use JohnStevenson\JsonWorks\Helpers\Utils;

class ItemsConstraint extends BaseConstraint implements ConstraintInterface
{
    /**
     * @param mixed $data
     * @param stdClass|array<mixed> $schema
     */
    public function validate($data, $schema, ?string $key = null): void
    {
        if (!is_array($data)) {
            $error = Utils::getArgumentError('$data', 'array', $data);
            throw new \InvalidArgumentException($error);
        }

        if (!($schema instanceof stdClass)) {
            $error = Utils::getArgumentError('$schema', 'sdtClass', $schema);
            throw new \InvalidArgumentException($error);
        }

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
     * @param array<mixed> $data
     */
    protected function validateObjectItems(array $data, stdClass $schema): void
    {
        $key = 0;

        foreach ($data as $value) {
            $this->manager->validate($value, $schema, strval($key));
            ++$key;
        }
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $items
     * @param object|boolean|null $additional
     */
    protected function checkArrayItems(array $data, array $items, $additional): void
    {
        if (false === $additional) {
            if (count($data) > count($items)) {
                $this->addError('contains more elements than are allowed');
            }
        }
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $items
     * @param object|boolean|null $additional
     */
    protected function validateArrayItems(array $data, array $items, $additional): void
    {
        $key = 0;
        $itemsCount = count($items);

        foreach ($data as $value) {

            if ($key < $itemsCount) {
                /** @var array<mixed> $item */
                $item = $items[$key];
                $this->manager->validate($value, $item, strval($key));
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
