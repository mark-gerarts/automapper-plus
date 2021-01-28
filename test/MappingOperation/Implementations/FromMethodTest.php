<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use AutoMapperPlus\Test\Models\Visibility\Visibility;
use PHPUnit\Framework\TestCase;

/**
 * Class FromMethodTest
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class FromMethodTest extends TestCase
{
    public function testItMapsAMethod()
    {
        $operation = new FromMethod('getMethodValue');
        $operation->setOptions(Options::default());

        $source = new Visibility();
        $destination = new Destination();

        $operation->mapProperty('name', $source, $destination);

        $this->assertSame('foo', $destination->name);
    }
}
