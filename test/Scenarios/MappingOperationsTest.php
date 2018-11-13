<?php

namespace AutoMapperPlus\Test\Scenarios;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\MappingOperation\Operation;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use AutoMapperPlus\Test\Models\SimpleProperties\Source;
use PHPUnit\Framework\TestCase;

class MappingOperationsTest extends TestCase
{
    public function testSetToAlwaysSetsToTheSameValue()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(Source::class, Destination::class)
            ->forMember('name', Operation::setTo('some value'));
        $mapper = new AutoMapper($config);
        $source = new Source();
        $source->name = 'original value';

        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('some value', $result->name);
    }
}
