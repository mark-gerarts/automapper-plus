<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\AutoMapperConfig;
use PHPUnit\Framework\TestCase;
use Test\Models\SimpleProperties\Destination;
use Test\Models\SimpleProperties\Source;

/**
 * Class IgnoreTest
 *
 * @package AutoMapperPlus\MappingOperation
 */
class IgnoreTest extends TestCase
{
    public function testItIgnores()
    {
        $source = new Source();
        $source->name = 'SourceName';
        $destination = new Destination();
        $destination->name = 'DestinationName';

        $ignore = new Ignore();
        $ignore($source, $destination, 'name', new AutoMapperConfig());

        $this->assertEquals('DestinationName', $destination->name);
    }
}
