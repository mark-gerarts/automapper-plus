<?php
/**
 * Created by PhpStorm.
 * User: Veaceslav Vasilache <veaceslav.vasilache@gmail.com>
 * Date: 6/1/18
 * Time: 10:31 AM
 */

namespace AutoMapperPlus\MappingOperation\Implementations;


use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use PHPUnit\Framework\TestCase;

class FromKeyTest extends TestCase
{

    public function testMapProperty_Success()
    {
        // Arrange
        $elementKeyName = 'anotherProperty';
        $elementValue = 'Test value';

        $operation = new FromKey($elementKeyName);
        $operation->setOptions(Options::default());

        $sources = [$elementKeyName => $elementValue];

        $destination = new Destination();

        $operation->mapProperty($elementKeyName, $sources, $destination);

        $this->assertEquals($elementValue, $destination->anotherProperty);
    }
}
