<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\MappingOperation\Operation;
use PHPUnit\Framework\TestCase;
use Test\Models\SimpleProperties\Destination;
use Test\Models\SimpleProperties\Source;

/**
 * Class MappingTest
 *
 * @package AutoMapperPlus\Configuration
 */
class MappingTest extends TestCase
{
    public function testItCanAddAMappingCallback()
    {
        $mapping = new Mapping(Source::class, Destination::class, Operation::getProperty());
        $callable = function() {};
        $mapping->forMember('name', $callable);

        $this->assertEquals(Operation::mapFrom($callable), $mapping->getMappingCallbackFor('name'));
    }
}
