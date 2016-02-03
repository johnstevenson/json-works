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

use JohnStevenson\JsonWorks\Schema\Constraints\Manager;
use JohnStevenson\JsonWorks\Schema\Constraints\MaxMinConstraint;

class ObjectConstraint extends BaseConstraint
{
    protected $maxMin;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->maxMin = new MaxMinConstraint($manager);
    }

    protected function run($data, $schema, $key = null)
    {
        // maxProperties
        $this->maxMin->check($data, $schema, 'maxProperties');

        # minProperties
        $this->maxMin->check($data, $schema, 'minProperties');

        if (isset($schema->required)) {
            foreach ((array) $schema->required as $name) {
                if (!isset($data->$name)) {
                    $this->addError(sprintf("is missing required property '%s'", $name));
                }
            }
        }

        # additionalProperties
        $additional = $this->get($schema, 'additionalProperties', true);

        if (false === $additional) {
            $this->validateObjectWork($data, $schema);
        }

        $this->validateObjectChildren($data, $schema, $additional);
    }

    protected function validateObjectWork($data, $schema)
    {
        $set = (array) $data;
        $p = $this->get($schema, 'properties', new \stdClass());

        foreach ($p as $key => $value) {
            if (isset($set[$key])) {
                unset($set[$key]);
            }
        }

        $pp = $this->get($schema, 'patternProperties', new \stdClass());
        $setCopy = $set;

        foreach ($setCopy as $key => $value) {

            foreach ($pp as $regex => $val) {
                if ($this->match($regex, $key)) {
                    unset($set[$key]);
                    break;
                }
            }
        }

        if (!empty($set)) {
            $this->addError('contains unspecified additional properties');
        }
    }

    protected function validateObjectChildren($data, $schema, $additional)
    {
        if (true === $additional) {
            $additional = new \stdClass();
        }

        $p = $this->get($schema, 'properties', new \stdClass());
        $pp = $this->get($schema, 'patternProperties', new \stdClass());

        foreach ($data as $key => $value) {

            $child = array();

            if (isset($p->$key)) {
                $child[] = $p->$key;
            }

            foreach ($pp as $regex => $val) {
                if ($this->match($regex, $key)) {
                    $child[] = $val;
                }
            }

            if (empty($child) && $additional) {
                $child[] = $additional;
            }

            foreach ($child as $subSchema) {
                $this->validateChild($value, $subSchema, $key);
            }
        }
    }
}
