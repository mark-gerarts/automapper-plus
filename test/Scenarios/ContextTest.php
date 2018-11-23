<?php

namespace AutoMapperPlus\Test\Scenarios;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\MappingOperation\Implementations\MapTo;
use AutoMapperPlus\MappingOperation\Operation;
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
            ->forMember('name', function ($source, $mapper, $context= []) {
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
}
