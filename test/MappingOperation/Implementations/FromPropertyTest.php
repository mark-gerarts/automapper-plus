<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\Configuration\Options;
use PHPUnit\Framework\TestCase;
use Test\Models\SimpleProperties\Destination;
use Test\Models\Visibility\Visibility;

/**
 * Class FromPropertyTest
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class FromPropertyTest extends TestCase
{
    public function testItMapsAProperty()
    {
        $operation = new FromProperty('privateProperty');
        $operation->setOptions(Options::default());

        $source = new Visibility();
        $destination = new Destination();

        $operation->mapProperty('name', $source, $destination);

        $this->assertTrue($destination->name);
    }
}
