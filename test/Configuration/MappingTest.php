<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\MappingOperation\Operation;
use PHPUnit\Framework\TestCase;

/**
 * Class MappingTest
 *
 * @package AutoMapperPlus\Configuration
 */
class MappingTest extends TestCase
{
    public function testItCanAddAMappingCallback()
    {
        $mapping = new Mapping('From', 'To', Operation::getProperty());
        $callable = function() {};
        $mapping->forMember('property', $callable);

        $this->assertEquals(Operation::mapFrom($callable), $mapping->getMappingCallbackFor('property'));
    }
}
