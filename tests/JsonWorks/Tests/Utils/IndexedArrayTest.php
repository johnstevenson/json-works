<?php

namespace JsonWorks\Tests\Utils;

use \JohnStevenson\JsonWorks\Utils as Utils;

class IndexedArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testWithArray()
    {
        $value = array(1, 2, 3);
        $this->assertTrue(Utils::indexedArray($value));
    }

    public function testWithAssoc()
    {
        $value = array('firstname' => 'Fred', 'lastName' => 'Bloggs');
        $this->assertFalse(Utils::indexedArray($value));
    }

    public function testWithMixed()
    {
        $value = array(0, 1, 'firstname' => 'Fred', 'lastName' => 'Bloggs');
        $this->assertFalse(Utils::indexedArray($value));
    }
}
