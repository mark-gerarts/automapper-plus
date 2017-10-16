<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\MappingOperation\Operation;
use AutoMapperPlus\NameConverter\NamingConvention\SnakeCaseNamingConvention;
use AutoMapperPlus\NameResolver\IdentityNameResolver;
use PHPUnit\Framework\TestCase;
use Test\Models\SimpleProperties\Destination;
use Test\Models\SimpleProperties\Source;

/**
 * Class MappingTest
 *
 * @package AutoMapperPlus\Configuration
 */
class MappingTest extends TestCase
{
    public function testItCanAddAMappingCallback()
    {
        $autoMapperConfig = new AutoMapperConfig();
        $mapping = new Mapping(
            Source::class,
            Destination::class,
            $autoMapperConfig
        );
        $callable = function() { return 'x'; };
        $mapping->forMember('name', $callable);

        $expected = Operation::mapFrom($callable);
        $expected->setOptions($autoMapperConfig->getOptions());

        $this->assertEquals($expected, $mapping->getMappingOperationFor('name'));
    }

    public function testItReturnsTheCorrectClassNames()
    {
        $mapping = new Mapping(
            Source::class,
            Destination::class,
            new AutoMapperConfig()
        );

        $this->assertEquals(Source::class, $mapping->getSourceClassName());
        $this->assertEquals(Destination::class, $mapping->getDestinationClassName());
    }

    public function testItCanReverseMap()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(Source::class, Destination::class)->reverseMap();

        $this->assertTrue($config->hasMappingFor(Destination::class, Source::class));
    }

    public function testTheOptionsCanBeOverridden()
    {
        $config = new AutoMapperConfig();
        $initialOptions = $config->getOptions();

        $mapping = new Mapping(
            Source::class,
            Destination::class,
            $config
        );

        $mapping->setDefaults(function (Options $options) {
            $options->setDestinationMemberNamingConvention(new SnakeCaseNamingConvention());
        });

        $this->assertEquals(
            $mapping->getOptions()->getDestinationMemberNamingConvention(),
            new SnakeCaseNamingConvention()
        );
        // Ensure the parent options aren't changed.
        $this->assertEquals($initialOptions, $config->getOptions());
    }
}
