<?php

namespace AutoMapperPlus\Test\Scenarios;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\DataType;
use AutoMapperPlus\MappingOperation\Operation;
use AutoMapperPlus\Test\Models\Nested\ChildClass;
use AutoMapperPlus\Test\Models\Nested\ChildClassDto;
use AutoMapperPlus\Test\Models\Nested\ParentClass;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use PHPUnit\Framework\TestCase;

class ArrayMappingTest extends TestCase
{
    public function testItPerformsASimpleMapppingFromArray()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(DataType::ARRAY, Destination::class);
        $mapper = new AutoMapper($config);

        $source = ['name' => 'John Doe'];
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('John Doe', $result->name);
    }

    public function testItPerformsAFromPropertyOperation()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(DataType::ARRAY, Destination::class)
            ->forMember('name', Operation::fromProperty('full_name'));
        $mapper = new AutoMapper($config);

        $source = ['full_name' => 'John Doe'];
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('John Doe', $result->name);
    }

    public function testItPerformsAIgnoreOperation()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(DataType::ARRAY, Destination::class)
            ->forMember('name', Operation::ignore());
        $mapper = new AutoMapper($config);

        $source = ['name' => 'John Doe'];
        $result = $mapper->map($source, Destination::class);

        $this->assertTrue(empty($result->name));
    }

    public function testItPerformsAMapFromOperation()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(DataType::ARRAY, Destination::class)
            ->forMember('name', Operation::mapFrom(
                function ($source) {
                    $this->assertEquals(['name' => 'John Doe'], $source);
                    return 'Doe John';
                }));
        $mapper = new AutoMapper($config);

        $source = ['name' => 'John Doe'];
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('Doe John', $result->name);
    }

    public function testItPerformsAMapToOperation()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(DataType::ARRAY, ParentClass::class)
            ->forMember('child', Operation::mapTo(ChildClass::class));
        $config->registerMapping(ChildClassDto::class, ChildClass::class);
        $mapper = new AutoMapper($config);

        $childDto = new ChildClassDto();
        $childDto->name = 'John Doe';
        $source = ['child' => $childDto];
        $result = $mapper->map($source, ParentClass::class);

        $this->assertInstanceOf(ChildClass::class, $result->child);
        $this->assertEquals('John Doe', $result->child->name);
    }

    public function testMapToHandlesAnArrayMapping()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(DataType::ARRAY, ParentClass::class)
            ->forMember('child', Operation::mapTo(ChildClass::class, true));
        $config->registerMapping(DataType::ARRAY, ChildClass::class);
        $mapper = new AutoMapper($config);

        $childDto = ['name' => 'John Doe'];
        $source = ['child' => $childDto];
        $result = $mapper->map($source, ParentClass::class);

        $this->assertEquals('John Doe', $result->child->name);
    }

    public function testMapToDefaultsToAssumingACollection()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(DataType::ARRAY, ParentClass::class)
            ->forMember('child', Operation::mapTo(ChildClass::class));
        $config->registerMapping(ChildClassDto::class, ChildClass::class);
        $mapper = new AutoMapper($config);

        $childDto = new ChildClassDto();
        $childDto->name = 'John Doe';
        $source = ['child' => [$childDto]];
        $result = $mapper->map($source, ParentClass::class);

        $this->assertInternalType('array', $result->child);
        $this->assertEquals('John Doe', $result->child[0]->name);
    }
}
