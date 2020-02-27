<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use AutoMapperPlus\Test\Models\SimpleProperties\DestinationAlt;
use AutoMapperPlus\Test\Models\SimpleProperties\Source;
use PHPUnit\Framework\TestCase;

/**
 * Class FromPropertyTest
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class SetToTest extends TestCase
{
    public function testItMapsAProperty()
    {
        $operation = new SetTo('always the same value');
        $operation->setOptions(Options::default());
        $source = new Source();
        $destination = new Destination();

        $operation->mapProperty('name', $source, $destination);

        $this->assertEquals('always the same value', $destination->name);
    }

    public function testItDoesntNeedTheSourceProperty()
    {
        $operation = new SetTo('always the same value');
        $operation->setOptions(Options::default());
        $source = new Source();
        $destination = new DestinationAlt();

        // anotherProperty is not present on the source object.
        $operation->mapProperty('anotherProperty', $source, $destination);

        $this->assertEquals('always the same value', $destination->anotherProperty);
    }
}
