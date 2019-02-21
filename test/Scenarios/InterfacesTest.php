<?php

namespace AutoMapperPlus\Test\Scenarios;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Exception\AutoMapperPlusException;
use AutoMapperPlus\Test\Models\Interfaces\DestinationImplementation;
use AutoMapperPlus\Test\Models\Interfaces\DestinationInterface;
use AutoMapperPlus\Test\Models\Interfaces\SourceImplementation;
use AutoMapperPlus\Test\Models\Interfaces\SourceInterface;
use PHPUnit\Framework\TestCase;

class InterfacesTest extends TestCase
{
    public function testItMapsFromAnInterface()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(SourceInterface::class, DestinationImplementation::class);
        $mapper = new AutoMapper($config);
        $source = new SourceImplementation('a name');

        $result = $mapper->map($source, DestinationImplementation::class);

        $this->assertEquals('a name', $result->name);
    }

    public function testItDoesntAllowMappingToAnInterface()
    {
        $this->expectException(AutoMapperPlusException::class);

        $config = new AutoMapperConfig();
        $config->registerMapping(SourceImplementation::class, DestinationInterface::class)
            ->dontSkipConstructor();
        $mapper = new AutoMapper($config);
        $source = new SourceImplementation('a name');

        $result = $mapper->map($source, DestinationInterface::class);

        $this->assertEquals('a name', $result->name);
    }

    public function testMappingToAnInterfaceIsAllowedForMapToObject()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(SourceImplementation::class, DestinationInterface::class);
        $mapper = new AutoMapper($config);
        $source = new SourceImplementation('a name');
        $destination = new DestinationImplementation();
        $result = $mapper->mapToObject($source, $destination);

        $this->assertEquals('a name', $result->name);
    }
}
