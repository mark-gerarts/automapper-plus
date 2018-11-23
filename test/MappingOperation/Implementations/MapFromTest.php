<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\MappingOperation\ContextAwareOperation;
use AutoMapperPlus\MappingOperation\MapperAwareOperation;
use PHPUnit\Framework\TestCase;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use AutoMapperPlus\Test\Models\SimpleProperties\Source;

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
        $operation->setOptions(Options::default());

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
        $operation->setOptions(Options::default());

        $source = new Source();
        $destination = new Destination();

        $operation->mapProperty('name', $source, $destination);

        $this->assertInstanceOf(Source::class, $destination->name);
    }

    public function testItImplementsMapperAwareOperation()
    {
        $operation = new MapFrom(function() {});

        $this->assertInstanceOf(MapperAwareOperation::class, $operation);
    }

    public function testItPassesTheMapperAsSecondParameter()
    {
        $operation = new MapFrom(function ($source = null, $mapper = null) {
            $this->assertInstanceOf(AutoMapperInterface::class, $mapper);
            return 'arbitrary value';
        });
        $mapper = AutoMapper::initialize(function() {});
        $options = $mapper->getConfiguration()->getOptions();
        $operation->setOptions($options);
        $operation->setMapper(AutoMapper::initialize(function () {}));

        $operation->mapProperty('name', new Source(), new Destination());
    }

    public function testMapFromIsContextAware()
    {
        $operation = new MapFrom(function () {});

        $this->assertInstanceOf(ContextAwareOperation::class, $operation);
    }
}
