<?php

namespace AutoMapperPlus\Test\Scenarios;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Test\Middleware\AnwserToUniverseMiddleware;
use AutoMapperPlus\Test\Middleware\AppendMapperMiddleware;
use AutoMapperPlus\Test\Middleware\AppendPropertyMiddleware;
use AutoMapperPlus\Test\Middleware\PrependMapperMiddleware;
use AutoMapperPlus\Test\Middleware\PrependPropertyMiddleware;
use AutoMapperPlus\Test\Middleware\ValueMapperDefaultMiddleware;
use AutoMapperPlus\Test\Middleware\ValueMapperMiddleware;
use AutoMapperPlus\Test\Middleware\ValuePropertyDefaultMiddleware;
use AutoMapperPlus\Test\Middleware\ValuePropertyMiddleware;
use AutoMapperPlus\Test\Models\Employee\Employee;
use AutoMapperPlus\Test\Models\Employee\EmployeeDto;
use AutoMapperPlus\Test\Models\SimpleProperties\CompleteSource;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use PHPUnit\Framework\TestCase;

class MiddlewareTest extends TestCase
{
    public function testValueMapperMiddleware()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new ValueMapperMiddleware('mapper middleware value', 'name'));
        $config->registerMapping(CompleteSource::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new CompleteSource('a name', 'another property');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('mapper middleware value', $result->name);
        $this->assertEquals('another property', $result->anotherProperty);
    }

    public function testValuePropertyMiddleware()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new ValuePropertyMiddleware('property middleware value', 'name'));
        $config->registerMapping(CompleteSource::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new CompleteSource('a name', 'another property');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('property middleware value', $result->name);
        $this->assertEquals('another property', $result->anotherProperty);
    }

    public function testValueMapperDefaultMiddleware()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new ValueMapperDefaultMiddleware('mapper middleware value', 'name'));
        $config->registerMapping(CompleteSource::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new CompleteSource('a name', 'another property');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('mapper middleware value', $result->name);
        $this->assertNull($result->anotherProperty);
    }

    public function testValuePropertyDefaultMiddleware()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new ValuePropertyDefaultMiddleware('property middleware value', 'name'));
        $config->registerMapping(CompleteSource::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new CompleteSource('a name', 'another property');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('property middleware value', $result->name);
        $this->assertNull($result->anotherProperty);
    }

    public function testAppendMapperMiddleware()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new AppendMapperMiddleware(' (value)', 'name'));
        $config->registerMapping(CompleteSource::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new CompleteSource('a name');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('a name (value)', $result->name);
    }

    public function testManyMiddlewares()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(
            new PrependMapperMiddleware('[before]', 'name'),
            new ValuePropertyMiddleware('mapper middleware value', 'name'),
            new PrependPropertyMiddleware('[before-property]', 'name'),
            new AppendPropertyMiddleware('[after-property]', 'name'),
            new AppendMapperMiddleware('[after]', 'name')
        );
        $config->registerMapping(CompleteSource::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new CompleteSource('a name', 'another property');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('[before][before-property]mapper middleware value[after-property][after]', $result->name);
        $this->assertEquals('another property', $result->anotherProperty);
    }

    public function testAnswerToUniverse()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new AnwserToUniverseMiddleware());
        $config->registerMapping(Employee::class, EmployeeDto::class);
        $config->registerMapping(
            \AutoMapperPlus\Test\Models\SimilarPropertyNames\Source::class,
            \AutoMapperPlus\Test\Models\SimilarPropertyNames\Destination::class
        );

        $mapper = new AutoMapper($config);
        $source1 = new Employee(NULL, 'John', 'Doe', 1980);
        $source2 = new \AutoMapperPlus\Test\Models\SimilarPropertyNames\Source(NULL, NULL);

        /** @var EmployeeDto $result1 */
        $result1 = $mapper->map($source1, EmployeeDto::class);
        $this->assertEquals(42, $result1->id);
        $this->assertEquals('John', $result1->firstName);
        $this->assertEquals('Doe', $result1->lastName);

        /**
         * @var \AutoMapperPlus\Test\Models\SimilarPropertyNames\Destination $result2
         */
        $result2 = $mapper->map($source2, \AutoMapperPlus\Test\Models\SimilarPropertyNames\Destination::class);
        $this->assertEquals(42, $result2->id);
        $this->assertNull($result2->second_id);

    }
}
