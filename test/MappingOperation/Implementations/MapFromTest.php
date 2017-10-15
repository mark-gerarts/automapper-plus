<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\Configuration\Configuration;
use PHPUnit\Framework\TestCase;
use Test\Models\SimpleProperties\Destination;
use Test\Models\SimpleProperties\Source;

/**
 * Class MapFromTest
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 * @group mappingOperations
 */
class MapFromTest extends TestCase
{
    public function testItMapsFromACallback()
    {
        $operation = new MapFrom(function ($source) {
            return 42;
        });
        $operation->setConfig(Configuration::default());

        $source = new Source();
        $destination = new Destination();

        $operation->mapProperty('name', $source, $destination);

        $this->assertEquals(42, $destination->name);
    }

    public function testItReceivesTheSourceObject()
    {
        $operation = new MapFrom(function ($source) {
            return $source;
        });
        $operation->setConfig(Configuration::default());

        $source = new Source();
        $destination = new Destination();

        $operation->mapProperty('name', $source, $destination);

        $this->assertInstanceOf(Source::class, $destination->name);
    }
}
