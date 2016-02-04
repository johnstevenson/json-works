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
use JohnStevenson\JsonWorks\Schema\Constraints\OfConstraint;

class AllOfConstraint extends BaseConstraint
{
    protected $of;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->of = new OfConstraint($manager);
    }

    protected function run($data, $schema, $key = null)
    {
        $this->of->check($data, $schema, 'allOf');
    }
}
