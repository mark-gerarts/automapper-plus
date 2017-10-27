<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Test\CustomMappingOperations\BasicOperation;
use AutoMapperPlus\Test\CustomMappingOperations\ReversibleOperation;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use AutoMapperPlus\Test\Models\SimpleProperties\Source;
use PHPUnit\Framework\TestCase;

/**
 * Class CustomOperationTest
 *
 * Tests if the AutoMapper respects the conventions for creating custom mapping
 * operations.
 *
 * @package AutoMapperPlus\MappingOperation
 */
class CustomOperationTest extends TestCase
{
    public function testACustomOperationCanBeUsed()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(Source::class, Destination::class)
            ->forMember('name', new BasicOperation());
        $mapper = new AutoMapper($config);

        $source = new Source();
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('BasicOperation', $result->name);
    }

    public function testAReversibleOperationCanBeUsed()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(Source::class, Destination::class)
            ->forMember('name', new ReversibleOperation())
            ->reverseMap();
        $mapper = new AutoMapper($config);

        $source = new Source();
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('ReversibleNormal', $result->name);

        $destination = new Destination();
        $result = $mapper->map($destination, Source::class);

        $this->assertEquals('ReversibleReversed', $result->name);
    }
}
