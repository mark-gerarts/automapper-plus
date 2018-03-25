<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Configuration\Options;
use PHPUnit\Framework\TestCase;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use AutoMapperPlus\Test\Models\SimpleProperties\Source;

/**
 * Class MapFromWithMapperTest
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 * @group mappingOperations
 */
class MapFromWithMapperTest extends TestCase
{
    public function testItMapsFromACallback()
    {
        // Arrange
        $mapFromWithMapper = new MapFromWithMapper(function ($source, AutoMapperInterface $mapper) {
            return 42;
        });
        $mapFromWithMapper->setMapper(AutoMapper::initialize(function (AutoMapperConfigInterface $config) {
            $config->registerMapping(Source::class, Destination::class);
        }));
        $mapFromWithMapper->setOptions(Options::default());

        $source = new Source();
        $destination = new Destination();

        // Act
        $mapFromWithMapper->mapProperty('name', $source, $destination);

        // Assert
        $this->assertEquals(42, $destination->name);
    }

    public function testItReceivesTheSourceObject()
    {
        // Arrange
        $mapFromWithMapper = new MapFromWithMapper(function ($source, AutoMapperInterface $mapper) {
            return $source;
        });
        $mapFromWithMapper->setMapper(AutoMapper::initialize(function (AutoMapperConfigInterface $config) {
            $config->registerMapping(Source::class, Destination::class);
        }));
        $mapFromWithMapper->setOptions(Options::default());

        $source = new Source();
        $destination = new Destination();

        // Act
        $mapFromWithMapper->mapProperty('name', $source, $destination);

        // Assert
        $this->assertInstanceOf(Source::class, $destination->name);
    }
}
