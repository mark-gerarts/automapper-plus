<?php

namespace AutoMapperPlus\Test\PropertyAccessor;

use PHPUnit\Framework\TestCase;
use AutoMapperPlus\PropertyAccessor\ObjectCratePropertyWriter;

/**
 * Class ObjectCratePropertyWriter
 *
 * @package AutoMapperPlus\Test\PropertyAccessor
 */
class ObjectCratePropertyWriterTest extends TestCase
{
    public function testItSetsAPublicProperty(): void
    {
        $writer = new ObjectCratePropertyWriter();
        $destination = new \stdClass();

        $writer->setProperty($destination, 'someProperty', 'some value');

        $this->assertTrue(isset($destination->someProperty));
        $this->assertEquals('some value', $destination->someProperty);
    }
}
