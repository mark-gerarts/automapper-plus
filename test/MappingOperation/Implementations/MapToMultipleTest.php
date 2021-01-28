<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use AutoMapperPlus\MappingOperation\ContextAwareOperation;
use AutoMapperPlus\NameResolver\CallbackNameResolver;
use AutoMapperPlus\Test\Models\Nested\ParentClass;
use AutoMapperPlus\Test\Models\Nested\ParentClassDto;
use AutoMapperPlus\Test\Models\Nested\PolymorphicChildA;
use AutoMapperPlus\Test\Models\Nested\PolymorphicChildB;
use AutoMapperPlus\Test\Models\Nested\PolymorphicDtoA;
use AutoMapperPlus\Test\Models\Nested\PolymorphicDtoB;
use PHPUnit\Framework\TestCase;

/**
 * Class MapToTest
 * @package AutoMapperPlus\MappingOperation\Implementations
 * @group mappingOperations
 */
class MapToMultipleTest extends TestCase
{
    public function testItCanBeInstantiated()
    {
        $mapToAnyOf = new MapToMultiple([PolymorphicDtoA::class, PolymorphicDtoB::class]);

        $this->assertEquals([PolymorphicDtoA::class, PolymorphicDtoB::class], $mapToAnyOf->getDestinationClassList());
    }

    public function testItCanMapToAClass()
    {
        $mapToAnyOf = new MapToMultiple([PolymorphicDtoA::class, PolymorphicDtoB::class]);
        $mapToAnyOf->setOptions(Options::default());
        $mapToAnyOf->setMapper(AutoMapper::initialize(function (AutoMapperConfigInterface $config) {
            $config->registerMapping(PolymorphicChildA::class, PolymorphicDtoA::class);
            $config->registerMapping(PolymorphicChildB::class, PolymorphicDtoB::class);
        }
        )
        );

        $parent = new ParentClass();
        $parent->polymorphicChildren = [new PolymorphicChildA('foo'), new PolymorphicChildB('bar')];
        $parentDestination = new ParentClassDto();

        $mapToAnyOf->mapProperty('polymorphicChildren', $parent, $parentDestination);

        $this->assertIsArray($parentDestination->polymorphicChildren);
        $this->assertInstanceOf(PolymorphicDtoA::class, $parentDestination->polymorphicChildren[0]);
        $this->assertEquals('foo', $parentDestination->polymorphicChildren[0]->name);

        $this->assertInstanceOf(PolymorphicDtoB::class, $parentDestination->polymorphicChildren[1]);
        $this->assertEquals('bar', $parentDestination->polymorphicChildren[1]->name);
    }

    public function testItCanMapSingle()
    {
        $mapToAnyOf = new MapToMultiple([PolymorphicDtoA::class, PolymorphicDtoB::class]);
        $mapToAnyOf->setOptions(Options::default());
        $mapToAnyOf->setMapper(AutoMapper::initialize(function (AutoMapperConfigInterface $config) {
            $config->registerMapping(PolymorphicChildA::class, PolymorphicDtoA::class);
            $config->registerMapping(PolymorphicChildB::class, PolymorphicDtoB::class);
        }
        )
        );

        $parent = new ParentClass();
        $parent->polymorphicChildren = new PolymorphicChildA('foo');
        $parentDestination = new ParentClassDto();

        $mapToAnyOf->mapProperty('polymorphicChildren', $parent, $parentDestination);
        $this->assertInstanceOf(PolymorphicDtoA::class, $parentDestination->polymorphicChildren);
        $this->assertEquals('foo', $parentDestination->polymorphicChildren->name);
    }

    public function testItCantMapToUnregistered()
    {
        $mapToAnyOf = new MapToMultiple([PolymorphicDtoA::class, PolymorphicDtoB::class]);
        $mapToAnyOf->setOptions(Options::default());
        $mapToAnyOf->setMapper(AutoMapper::initialize(function (AutoMapperConfigInterface $config) {
            $config->registerMapping(PolymorphicChildB::class, PolymorphicDtoB::class);
        }
        )
        );

        $parent = new ParentClass();
        $parent->polymorphicChildren = new PolymorphicChildA('foo');
        $parentDestination = new ParentClassDto();

        $this->expectException(UnregisteredMappingException::class);
        $mapToAnyOf->mapProperty('polymorphicChildren', $parent, $parentDestination);
    }

    /**
     * Ensure the operation uses the assigned name resolver. See #17.
     */
    public function testItUsesTheNameResolver()
    {
        $mapToAnyOf = new MapToMultiple([PolymorphicDtoA::class, PolymorphicDtoB::class]);
        $options = Options::default();
        // Set a name resolver to always use the property 'child' of the source.
        $options->setNameResolver(new CallbackNameResolver(function () {
            return 'polymorphicChildren';
        }
        )
        );
        $mapToAnyOf->setOptions($options);
        $mapToAnyOf->setMapper(AutoMapper::initialize(function (AutoMapperConfigInterface $config) {
            $config->registerMapping(PolymorphicChildA::class, PolymorphicDtoA::class);
            $config->registerMapping(PolymorphicChildB::class, PolymorphicDtoB::class);
        }
        )
        );

        $parent = new ParentClass();
        $parent->polymorphicChildren = [new PolymorphicChildA('foo'), new PolymorphicChildB('bar')];
        $parentDestination = new ParentClassDto();

        $mapToAnyOf->mapProperty('anotherProperty', $parent, $parentDestination);

        // Because of the name resolver, we expect the value to be set
        // correctly.
        $this->assertIsArray($parentDestination->anotherProperty);
        $this->assertInstanceOf(PolymorphicDtoA::class, $parentDestination->anotherProperty[0]);
        $this->assertEquals('foo', $parentDestination->anotherProperty[0]->name);

        $this->assertInstanceOf(PolymorphicDtoB::class, $parentDestination->anotherProperty[1]);
        $this->assertEquals('bar', $parentDestination->anotherProperty[1]->name);
    }

    public function testItIsContextAware()
    {
        $mapToAnyOf = new MapToMultiple([PolymorphicDtoA::class, PolymorphicDtoB::class]);

        $this->assertInstanceOf(ContextAwareOperation::class, $mapToAnyOf);
    }
}
