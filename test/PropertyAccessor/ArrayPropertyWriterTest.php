<?php

namespace AutoMapperPlus\Test\PropertyAccessor;

use AutoMapperPlus\PropertyAccessor\ArrayPropertyWriter;
use PHPUnit\Framework\TestCase;

class ArrayPropertyWriterTest extends TestCase
{
    public function testItWritesAProperty()
    {
        $writer = new ArrayPropertyWriter();
        $data = [];

        $writer->setProperty($data, 'a property', 'a value');

        $this->assertArrayHasKey('a property', $data);
        $this->assertEquals('a value', $data['a property']);
    }

    public function testItOverwritesAProperty()
    {
        $writer = new ArrayPropertyWriter();
        $data = [
            'a property' => 'old value'
        ];

        $writer->setProperty($data, 'a property', 'new value');

        $this->assertEquals('new value', $data['a property']);
    }
}
