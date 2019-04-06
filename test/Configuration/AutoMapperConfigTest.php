<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\MappingOperation\Operation;
use AutoMapperPlus\Test\Models\Inheritance\DestinationChild;
use AutoMapperPlus\Test\Models\Inheritance\DestinationParent;
use AutoMapperPlus\Test\Models\Inheritance\SourceChild;
use AutoMapperPlus\Test\Models\Inheritance\SourceParent;
use AutoMapperPlus\Test\Models\Interfaces\DestinationImplementation;
use AutoMapperPlus\Test\Models\Interfaces\DestinationInterface;
use AutoMapperPlus\Test\Models\Interfaces\SourceImplementation;
use AutoMapperPlus\Test\Models\Interfaces\SourceInterface;
use PHPUnit\Framework\TestCase;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use AutoMapperPlus\Test\Models\SimpleProperties\Source;

/**
 * Class AutoMapperConfigTest
 *
 * @package AutoMapperPlus\Configuration
 */
class AutoMapperConfigTest extends TestCase
{
    public function testItCanRegisterAMapping()
    {
        $config = new AutoMapperConfig();
        $mapping = $config->registerMapping(Source::class, Destination::class);

        $this->assertInstanceOf(MappingInterface::class, $mapping);
        $this->assertEquals(Source::class, $mapping->getSourceClassName());
        $this->assertEquals(Destination::class, $mapping->getDestinationClassName());
        $this->assertTrue($config->hasMappingFor(Source::class, Destination::class));
        $this->assertEquals($mapping, $config->getMappingFor(Source::class, Destination::class));
    }

    public function testGetMappingCanReturnNull()
    {
        $config = new AutoMapperConfig();

        $this->assertNull($config->getMappingFor(Source::class, Destination::class));
    }

    public function testOptionsCanBeSet()
    {
        $config = new AutoMapperConfig(function (Options $options) {
            $options->setDefaultMappingOperation(Operation::ignore());
        });

        $this->assertEquals(
            Operation::ignore(),
            $config->getOptions()->getDefaultMappingOperation()
        );
    }

    public function testSubstitutionPrincipleSource()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(SourceParent::class, DestinationParent::class);

        $this->assertTrue($config->hasMappingFor(
            SourceChild::class,
            DestinationParent::class
        ));
    }

    public function testSubstitutionPrincipleDestination()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(SourceParent::class, DestinationParent::class);

        $this->assertTrue($config->hasMappingFor(
            SourceParent::class,
            DestinationChild::class
        ));
    }

    public function testMappingsGetGeneratedOnTheFlyIfOptionSet()
    {
        $config = new AutoMapperConfig();
        $config->getOptions()->createUnregisteredMappings();

        $this->assertTrue($config->hasMappingFor(
            Source::class,
            Destination::class
        ));
    }

    public function testInterfacesAreLessSpecificThanClassesInTheSource()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(
            SourceInterface::class,
            DestinationImplementation::class
        );
        $concreteMapping = $config->registerMapping(
            SourceImplementation::class,
            DestinationImplementation::class
        );

        $result = $config->getMappingFor(
            SourceImplementation::class,
            DestinationImplementation::class
        );

        $this->assertEquals($concreteMapping, $result);
    }

    public function testInterfacesAreLessSpecificThanClassesInTheDestination()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(
            SourceImplementation::class,
            DestinationInterface::class
        );
        $concreteMapping = $config->registerMapping(
            SourceImplementation::class,
            DestinationImplementation::class
        );

        $result = $config->getMappingFor(
            SourceImplementation::class,
            DestinationImplementation::class
        );

        $this->assertEquals($concreteMapping, $result);
    }
}
