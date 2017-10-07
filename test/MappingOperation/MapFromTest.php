<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\AutoMapperConfig;
use PHPUnit\Framework\TestCase;
use Test\Models\SimpleProperties\Destination;
use Test\Models\SimpleProperties\Source;

class MapFromTest extends TestCase
{
    public function testItCanMapWithACallback()
    {
        $source = new Source();
        $destination = new Destination();
        $callback = function() { return 'Hello'; };
        $mapFrom = new MapFrom($callback);
        $mapFrom($source, $destination, 'name', new AutoMapperConfig());

        $this->assertEquals($destination->name, 'Hello');
    }
}
