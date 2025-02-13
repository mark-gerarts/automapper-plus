<?php

namespace AutoMapperPlus\Test\PropertyAccessor;

use AutoMapperPlus\PropertyAccessor\ArrayPropertyReader;
use PHPUnit\Framework\TestCase;

class ArrayPropertyReaderTest extends TestCase
{
    /**
     * @var ArrayPropertyReader
     */
    protected $reader;
    protected $array;

    protected function setUp(): void
    {
        $this->reader = new ArrayPropertyReader();
        $this->array = ['property' => 'value'];
    }

    public function testItDetectsIfAnArrayHasAProperty(): void
    {
        $this->assertTrue($this->reader->hasProperty(
            $this->array,
            'property'
        ));
        $this->assertFalse($this->reader->hasProperty(
            $this->array,
            'non-existing property'
        ));
    }

    public function testItCanGetAProperty(): void
    {
        $this->assertEquals(
            'value',
            $this->reader->getProperty($this->array, 'property')
        );
    }

    public function testItCanGetPropertyNames(): void
    {
        $this->assertEquals(
            ['property'],
            $this->reader->getPropertyNames($this->array)
        );
    }
}
