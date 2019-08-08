<?php

namespace AutoMapperPlus\Test\Scenarios;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Middleware\Middleware;
use AutoMapperPlus\Test\Middleware\AnwserToUniverseMiddleware;
use AutoMapperPlus\Test\Middleware\AppendMapperMiddleware;
use AutoMapperPlus\Test\Middleware\AppendPropertyMiddleware;
use AutoMapperPlus\Test\Middleware\BeforeMapperMiddleware;
use AutoMapperPlus\Test\Middleware\BeforePropertyMiddleware;
use AutoMapperPlus\Test\Middleware\NoopMapperMiddleware;
use AutoMapperPlus\Test\Middleware\NoopPropertyMiddleware;
use AutoMapperPlus\Test\Middleware\SkipMapperMiddleware;
use AutoMapperPlus\Test\Middleware\SkipPropertyMiddleware;
use AutoMapperPlus\Test\Middleware\ValueMapperMiddleware;
use AutoMapperPlus\Test\Middleware\ValuePropertyMiddleware;
use AutoMapperPlus\Test\Models\Employee\Employee;
use AutoMapperPlus\Test\Models\Employee\EmployeeDto;
use AutoMapperPlus\Test\Models\SimpleProperties\CompleteSource;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use AutoMapperPlus\Test\Models\SimpleProperties\Source;
use PHPUnit\Framework\TestCase;

class MiddlewareTest extends TestCase
{
    public function testValueMapperMiddleware()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new ValueMapperMiddleware());
        $config->registerMapping(Source::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new Source('a name');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('mapper middleware value', $result->name);
    }

    public function testValuePropertyMiddleware()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new ValuePropertyMiddleware());
        $config->registerMapping(CompleteSource::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new CompleteSource('a name', 'another property');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('property middleware value', $result->name);
        $this->assertEquals('another property', $result->anotherProperty);
    }

    public function testAppendMapperMiddleware()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new AppendMapperMiddleware());
        $config->registerMapping(Source::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new Source('a name');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('a name (append)', $result->name);
    }

    public function testAppendPropertyMiddleware()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new AppendPropertyMiddleware());
        $config->registerMapping(CompleteSource::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new CompleteSource('a name', 'another property');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('a name (append)', $result->name);
        $this->assertEquals('another property', $result->anotherProperty);
    }

    public function testBeforeNameMapperMiddleware()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new BeforeMapperMiddleware());
        $config->registerMapping(Source::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new Source('a name');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('a name', $result->name);
    }

    public function testBeforeNamePropertyMiddleware()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new BeforePropertyMiddleware());
        $config->registerMapping(CompleteSource::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new CompleteSource('a name', 'another property');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('a name', $result->name);
        $this->assertEquals('another property', $result->anotherProperty);
    }

    public function testSkipMapperMiddleware()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new SkipMapperMiddleware());
        $config->registerMapping(Source::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new Source('a name');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('a name', $result->name);
    }

    public function testSkipPropertyMiddleware()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new SkipPropertyMiddleware());
        $config->registerMapping(CompleteSource::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new CompleteSource('a name', 'another property');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('a name', $result->name);
        $this->assertEquals('This should happen unless it is name property', $result->anotherProperty);
    }

    public function testNoopMapperMiddleware()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new NoopMapperMiddleware());
        $config->registerMapping(Source::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new Source('a name');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertNull($result->name);
    }

    public function testNoopPropertyMiddleware()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(new NoopPropertyMiddleware());
        $config->registerMapping(CompleteSource::class, Destination::class);
        $mapper = new AutoMapper($config);
        $source = new CompleteSource('a name', 'another property');

        /** @var Destination $result */
        $result = $mapper->map($source, Destination::class);

        $this->assertNull($result->name);
        $this->assertEquals('another property', $result->anotherProperty);
    }

    public function testManyMiddlewares()
    {
        $config = new AutoMapperConfig();
        $config->registerMiddlewares(
            new AppendPropertyMiddleware('mapper middleware value', Middleware::OVERRIDE),
            new AppendMapperMiddleware('[before]', Middleware::BEFORE),
            new AppendPropertyMiddleware('[before-property]', Middleware::BEFORE),
            new AppendMapperMiddleware('[after]'),
            new AppendPropertyMiddleware('[after-property]')
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
