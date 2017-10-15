<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use PHPUnit\Framework\TestCase;
use Test\Models\SimpleProperties\Destination;
use Test\Models\SimpleProperties\Source;

/**
 * Class IgnoreTest
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 * @group mappingOperations
 */
class IgnoreTest extends TestCase
{
    public function testItIgnores()
    {
        $operation = new Ignore();

        $source = new Source();
        $source->name = 'Source';
        $destination = new Destination();
        $destination->name = 'Destination';

        $operation->mapProperty('name', $source, $destination);

        $this->assertEquals('Destination', $destination->name);
    }
}
