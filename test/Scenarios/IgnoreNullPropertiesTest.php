<?php

namespace AutoMapperPlus\Test\Scenarios;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\MappingOperation\Operation;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use AutoMapperPlus\Test\Models\SimpleProperties\Source;
use PHPUnit\Framework\TestCase;

class IgnoreNullPropertiesTest extends TestCase
{
    public function testItMapsToNullIfSourceIsNull()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(Source::class, Destination::class);
        $mapper = new AutoMapper($config);

        $source = new Source(null);
        $destination = new Destination();
        $destination->name = 'Hello, world';
        $mapper->mapToObject($source, $destination);

        $this->assertNull($destination->name);
    }

    public function testItDoesntMapToNullIfOptionIsSet()
    {
        $config = new AutoMapperConfig();
        $config->getOptions()->ignoreNullProperties();
        $config->registerMapping(Source::class, Destination::class);
        $mapper = new AutoMapper($config);

        $source = new Source(null);
        $destination = new Destination();
        $destination->name = 'Hello, world';
        $mapper->mapToObject($source, $destination);

        $this->assertEquals('Hello, world', $destination->name);
    }

    public function testMapFromIsntAffectedByIgnoreNullOption()
    {
        $config = new AutoMapperConfig();
        $config->getOptions()->ignoreNullProperties();
        $config->registerMapping(Source::class, Destination::class)
            ->forMember('name', Operation::mapFrom(function () { return null; }));
        $mapper = new AutoMapper($config);

        $source = new Source(null);
        $destination = new Destination();
        $destination->name = 'Hello, world';
        $mapper->mapToObject($source, $destination);

        $this->assertEquals(null, $destination->name);
    }
}
