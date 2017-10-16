<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\Options;
use PHPUnit\Framework\TestCase;
use Test\Models\SimpleProperties\Destination;
use Test\Models\SimpleProperties\Source;

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

    public function setUp()
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
}
