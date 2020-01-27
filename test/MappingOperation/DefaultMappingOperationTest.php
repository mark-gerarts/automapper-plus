<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\NameConverter\NamingConvention\CamelCaseNamingConvention;
use AutoMapperPlus\NameConverter\NamingConvention\SnakeCaseNamingConvention;
use PHPUnit\Framework\TestCase;
use AutoMapperPlus\Test\Models\NamingConventions\CamelCaseSource;
use AutoMapperPlus\Test\Models\NamingConventions\SnakeCaseSource;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use AutoMapperPlus\Test\Models\SimpleProperties\Source;

/**
 * Class DefaultMappingOperationTest
 *
 * @package AutoMapperPlus\MappingOperation
 * @group mappingOperations
 */
class DefaultMappingOperationTest extends TestCase
{
    /**
     * @var DefaultMappingOperation
     */
    protected $operation;

    public function setUp(): void
    {
        $this->operation = new DefaultMappingOperation();
        $this->operation->setOptions(Options::default());
    }

    public function testItMapsAProperty()
    {
        $source = new Source();
        $source->name = 'Hello, world';
        $destination = new Destination();

        $this->operation->mapProperty('name', $source, $destination);

        $this->assertEquals('Hello, world', $destination->name);
    }

    public function testItCanResolveNamingConventions()
    {
        $options = Options::default();
        $options->setSourceMemberNamingConvention(new CamelCaseNamingConvention());
        $options->setDestinationMemberNamingConvention(new SnakeCaseNamingConvention());
        $this->operation->setOptions($options);

        $source = new CamelCaseSource();
        $source->propertyName = 'ima property';
        $destination = new SnakeCaseSource();
        $this->operation->mapProperty('property_name', $source, $destination);

        $this->assertEquals('ima property', $destination->property_name);
    }

    /**
     * @group stdClass
     */
    public function testItCanMapFromAStdClass()
    {
        $source = new \stdClass();
        $source->name = 'stdclass property';
        $destination = new Destination();

        $this->operation->mapProperty('name', $source, $destination);

        $this->assertEquals('stdclass property', $destination->name);
    }

    public function testItCanResolveNamingConventionsOnAStdClass()
    {
        $options = Options::default();
        $options->setSourceMemberNamingConvention(new SnakeCaseNamingConvention());
        $options->setDestinationMemberNamingConvention(new CamelCaseNamingConvention());
        $this->operation->setOptions($options);

        $source = new \stdClass();
        $source->property_name = 'stdclass snake';
        $destination = new CamelCaseSource();
        $this->operation->mapProperty('propertyName', $source, $destination);

        $this->assertEquals('stdclass snake', $destination->propertyName);
    }
}
