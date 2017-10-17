<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Configuration\Options;
use PHPUnit\Framework\TestCase;
use Test\Models\Nested\ChildClass;
use Test\Models\Nested\ParentClass;
use Test\Models\Nested\ParentClassDto;
use Test\Models\SimpleProperties\Destination;
use Test\Models\SimpleProperties\Source;

/**
 * Class MapToTest
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 * @group mappingOperations
 */
class MapToTest extends TestCase
{
    public function testItCanBeInstantiated()
    {
        $mapTo = new MapTo(Destination::class);

        $this->assertEquals(Destination::class, $mapTo->getDestinationClass());
    }

    public function testItCanMapToAClass()
    {
        $mapTo = new MapTo(Destination::class);
        $mapTo->setOptions(Options::default());
        $mapTo->setMapper(AutoMapper::initialize(function (AutoMapperConfigInterface $config) {
            $config->registerMapping(Source::class, Destination::class);
        }));

        $parent = new ParentClass();
        $child = new Source('SourceName');
        $parent->child = $child;
        $parentDestination = new ParentClassDto();

        $mapTo->mapProperty('child', $parent, $parentDestination);

        $this->assertInstanceOf(Destination::class, $parentDestination->child);
        $this->assertEquals('SourceName', $parentDestination->child->name);
    }

    public function testItCanMapMultiple()
    {
        $mapTo = new MapTo(Destination::class);
        $mapTo->setOptions(Options::default());
        $mapTo->setMapper(AutoMapper::initialize(function (AutoMapperConfigInterface $config) {
            $config->registerMapping(Source::class, Destination::class);
        }));

        $parent = new ParentClass();
        $children = [
            new Source('SourceName1'),
            new Source('SourceName2'),
            new Source('SourceName3')
        ];
        $parent->child = $children;
        $parentDestination = new ParentClassDto();

        $mapTo->mapProperty('child', $parent, $parentDestination);

        $this->assertEquals(count($children), count($parentDestination->child));
        $this->assertEquals('SourceName1', $parentDestination->child[0]->name);
        $this->assertInstanceOf(Destination::class, $parentDestination->child[1]);
    }
}
