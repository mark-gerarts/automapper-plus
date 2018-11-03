<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\PropertyAccessor\PropertyAccessorInterface;
use AutoMapperPlus\PropertyAccessor\PropertyReaderInterface;
use AutoMapperPlus\PropertyAccessor\PropertyWriterInterface;
use AutoMapperPlus\Test\Models\SimpleProperties\Source;
use PHPUnit\Framework\TestCase;

/**
 * Class OptionsTest
 *
 * @package AutoMapperPlus\Configuration
 */
class OptionsTest extends TestCase
{
    public function testItCanRegisterAnObjectCrate()
    {
        $options = new Options();

        $this->assertFalse($options->isObjectCrate(Source::class));
        $options->registerObjectCrate(Source::class);
        $this->assertTrue($options->isObjectCrate(Source::class));
    }

    public function testPropertyWriterCanBeOverridden()
    {
        $options = new Options();
        $accessor = $this->createMock(PropertyAccessorInterface::class);
        $writer = $this->createMock(PropertyWriterInterface::class);
        $options->setPropertyAccessor($accessor);
        $options->setPropertyWriter($writer);

        $this->assertEquals($accessor, $options->getPropertyAccessor());
        $this->assertEquals($writer, $options->getPropertyWriter());
    }

    public function testPropertyReaderCanBeOverridden()
    {
        $options = new Options();
        $accessor = $this->createMock(PropertyAccessorInterface::class);
        $reader = $this->createMock(PropertyReaderInterface::class);
        $options->setPropertyAccessor($accessor);
        $options->setPropertyReader($reader);

        $this->assertEquals($accessor, $options->getPropertyAccessor());
        $this->assertEquals($reader, $options->getPropertyReader());
    }

    public function testPropertyWriterDefaultsToAccessor()
    {
        $options = new Options();
        $accessor = $this->createMock(PropertyAccessorInterface::class);
        $options->setPropertyAccessor($accessor);

        $this->assertEquals($accessor, $options->getPropertyWriter());
    }

    public function testPropertyReaderDefaultsToAccessor()
    {
        $options = new Options();
        $accessor = $this->createMock(PropertyAccessorInterface::class);
        $options->setPropertyAccessor($accessor);

        $this->assertEquals($accessor, $options->getPropertyReader());
    }
}
