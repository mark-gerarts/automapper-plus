<?php

namespace AutoMapperPlus\Test\Scenarios;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\MappingOperation\Implementations\MapTo;
use AutoMapperPlus\MappingOperation\Operation;
use AutoMapperPlus\Test\Models\Nested\ChildClass;
use AutoMapperPlus\Test\Models\Nested\ChildClassDto;
use AutoMapperPlus\Test\Models\Nested\ParentClass;
use AutoMapperPlus\Test\Models\Nested\ParentClassDto;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use AutoMapperPlus\Test\Models\SimpleProperties\Source;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    public function testContextCanBePassedToMap()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(Source::class, Destination::class)
            ->forMember('name', function ($source, $mapper, $context = []) {
                $this->assertArrayHasKey('context_key', $context);
                $this->assertEquals('context-value', $context['context_key']);

                return $context['context_key'];
            });
        $mapper = new AutoMapper($config);
        $source = new Source('a name');

        $result = $mapper->map(
            $source,
            Destination::class,
            ['context_key' => 'context-value']
        );

        $this->assertEquals('context-value', $result->name);
    }

    public function testContextCanBePassedToMapToObject()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(Source::class, Destination::class)
            ->forMember('name', function ($source, $mapper, $context = []) {
                $this->assertArrayHasKey('context_key', $context);
                $this->assertEquals('context-value', $context['context_key']);

                return $context['context_key'];
            });
        $mapper = new AutoMapper($config);
        $source = new Source('a name');

        $result = $mapper->mapToObject(
            $source,
            new Destination(),
            ['context_key' => 'context-value']
        );

        $this->assertEquals('context-value', $result->name);
    }

    public function testContextCanBePassedToMapMultiple()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(Source::class, Destination::class)
            ->forMember('name', function ($source, $mapper, $context = []) {
                $this->assertArrayHasKey('context_key', $context);
                $this->assertEquals('context-value', $context['context_key']);

                return $context['context_key'];
            });
        $mapper = new AutoMapper($config);
        $source = new Source('a name');

        $result = $mapper->mapMultiple(
            [$source],
            Destination::class,
            ['context_key' => 'context-value']
        );

        $this->assertEquals('context-value', $result[0]->name);
    }

    public function testMapToUsesTheParentContext()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(\stdClass::class, \stdClass::class)
            ->forMember('source', Operation::mapTo(Destination::class));
        $config->registerMapping(Source::class, Destination::class)
            ->forMember('name', function ($source, $mapper, $context = []) {
                $this->assertArrayHasKey('context_key', $context);
                $this->assertEquals('context-value', $context['context_key']);

                return $context['context_key'];
            });
        $parent = new \stdClass();
        $parent->source = new Source('my name');
        $mapper = new AutoMapper($config);

        $result = $mapper->map($parent, \stdClass::class, ['context_key' => 'context-value']);

        $this->assertEquals('context-value', $result->source->name);
    }

    public function testMapToMergesTheParentContext()
    {
        $config = new AutoMapperConfig();
        $mapTo = new MapTo(
            Destination::class,
            false,
            ['map_to_key' => 'map-to-value']
        );
        $config->registerMapping(\stdClass::class, \stdClass::class)
            ->forMember('source', $mapTo);
        $config->registerMapping(Source::class, Destination::class)
            ->forMember('name', function ($source, $mapper, $context = []) {
                // Parent key still exists.
                $this->assertArrayHasKey('parent_key', $context);
                $this->assertEquals('parent-value', $context['parent_key']);

                // MapTo context is added as well.
                $this->assertArrayHasKey('map_to_key', $context);
                $this->assertEquals('map-to-value', $context['map_to_key']);

                return $context['map_to_key'];
            });
        $parent = new \stdClass();
        $parent->source = new Source('my name');
        $mapper = new AutoMapper($config);

        $result = $mapper->map($parent, \stdClass::class, ['parent_key' => 'parent-value']);

        $this->assertEquals('map-to-value', $result->source->name);
    }

    public function testDestinationClassIsPassed()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(Source::class, Destination::class)
            ->forMember('name', function ($source, $mapper, $context = []) {
                $this->assertArrayHasKey(
                    AutoMapper::DESTINATION_CONTEXT,
                    $context
                );
                $this->assertArrayHasKey(
                    AutoMapper::DESTINATION_CLASS_CONTEXT,
                    $context
                );
                $this->assertInstanceOf(
                    Destination::class,
                    $context[AutoMapper::DESTINATION_CONTEXT]
                );
                $this->assertEquals(
                    Destination::class,
                    $context[AutoMapper::DESTINATION_CLASS_CONTEXT]
                );

                return 'some value';
            });
        $mapper = new AutoMapper($config);
        $source = new Source('a name');

        $mapper->map(
            $source,
            Destination::class
        );
    }

    public function testMapToBuildsContextStacks()
    {
        $parent = new ParentClass();
        $parent->child = new ChildClass();

        $config = new AutoMapperConfig();
        $config->registerMapping(ParentClass::class, ParentClassDto::class)
            ->forMember('child', Operation::mapTo(ChildClassDto::class));

        $config->registerMapping(ChildClass::class, ChildClassDto::class)
            ->forMember('name', function ($source, $mapper, $context = []) use ($parent) {
                $this->assertArrayHasKey(
                    AutoMapper::SOURCE_STACK_CONTEXT,
                    $context
                );

                $this->assertEquals([$parent, $parent->child], $context[AutoMapper::SOURCE_STACK_CONTEXT]);

                $this->assertArrayHasKey(
                    AutoMapper::DESTINATION_STACK_CONTEXT,
                    $context
                );

                $this->assertCount(2, $context[AutoMapper::DESTINATION_STACK_CONTEXT]);
                $this->assertInstanceOf(ParentClassDto::class, $context[AutoMapper::DESTINATION_STACK_CONTEXT][0]);
                $this->assertInstanceOf(ChildClassDto::class, $context[AutoMapper::DESTINATION_STACK_CONTEXT][1]);

                $this->assertEquals($context[AutoMapper::DESTINATION_CONTEXT], $context[AutoMapper::DESTINATION_STACK_CONTEXT][1]);

                $this->assertArrayHasKey(
                    AutoMapper::PROPERTY_STACK_CONTEXT,
                    $context
                );

                $this->assertEquals($context[AutoMapper::PROPERTY_STACK_CONTEXT], ['child', 'name']);
            });

        $mapper = new AutoMapper($config);

        $mapper->map(
            $parent,
            ParentClassDto::class
        );
    }
}
