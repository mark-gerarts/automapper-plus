<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\MappingOperation\ContextAwareOperation;
use AutoMapperPlus\NameResolver\CallbackNameResolver;
use PHPUnit\Framework\TestCase;
use AutoMapperPlus\Test\Models\Nested\ParentClass;
use AutoMapperPlus\Test\Models\Nested\ParentClassDto;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use AutoMapperPlus\Test\Models\SimpleProperties\Source;

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

    /**
     * Ensure the operation uses the assigned name resolver. See #17.
     */
    public function testItUsesTheNameResolver()
    {
        $mapTo = new MapTo(Destination::class);
        $options = Options::default();
        // Set a name resolver to always use the property 'child' of the source.
        $options->setNameResolver(new CallbackNameResolver(function () {
            return 'child';
        }));
        $mapTo->setOptions($options);
        $mapTo->setMapper(AutoMapper::initialize(function (AutoMapperConfigInterface $config) {
            $config->registerMapping(Source::class, Destination::class);
        }));

        $parent = new ParentClass();
        $child = new Source('SourceName');
        $parent->child = $child;
        $parentDestination = new ParentClassDto();

        $mapTo->mapProperty('anotherProperty', $parent, $parentDestination);

        // Because of the name resolver, we expect the value to be set
        // correctly.
        $this->assertInstanceOf(Destination::class, $parentDestination->anotherProperty);
        $this->assertEquals('SourceName', $parentDestination->anotherProperty->name);
    }

    public function testItIsContextAware()
    {
        $mapTo = new MapTo(\stdClass::class);

        $this->assertInstanceOf(ContextAwareOperation::class, $mapTo);
    }
}
