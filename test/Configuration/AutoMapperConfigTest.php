<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\MappingOperation\Operation;
use AutoMapperPlus\NameConverter\NamingConvention\SnakeCaseNamingConvention;
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
}
