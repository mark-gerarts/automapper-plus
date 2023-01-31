<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\Configuration\Options;
use PHPUnit\Framework\TestCase;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use AutoMapperPlus\Test\Models\Visibility\Visibility;
use AutoMapperPlus\Test\Models\Nested\Person;
use AutoMapperPlus\Test\Models\Nested\PersonDto;
use AutoMapperPlus\Test\Models\Nested\Address;
use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPLus\Configuration\AutoMapperConfig;

/**
 * Class FromPropertyTest
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class FromPropertyTest extends TestCase
{
    public function testItMapsAProperty(): void
    {
        $operation = new FromProperty('privateProperty');
        $operation->setOptions(Options::default());

        $source = new Visibility();
        $destination = new Destination();

        $operation->mapProperty('name', $source, $destination);

        $this->assertTrue($destination->name);
    }

    public function testItPassesContext(): void
    {
        // Create a dummy mapper that can be passed to the operation and can be
        // used to check if context is set.
        $dummyMapper = new class($this) implements AutoMapperInterface {

            private FromPropertyTest $testCase;

            public function __construct(FromPropertyTest $testCase)
            {
                $this->testCase = $testCase;
            }

            public function map($source, string $targetClass, array $context = [])
            {
                $this->testCase->assertArrayHasKey('some', $context);
            }

            public function mapToObject($source, $destination, array $context = [])
            {
                return null;
            }

            public function mapMultiple(
                $sourceCollection,
                string $targetClass,
                array $context = []
            ): array {
                return [];
            }

            public static function initialize(callable $configurator): AutoMapperInterface
            {
                return $this;
            }

            public function getConfiguration(): AutoMapperConfigInterface
            {
                return new AutoMapperConfig();
            }
        };

        $operation = new FromProperty('address');
        $operation->mapTo(Address::class);
        $operation->setOptions(Options::default());
        $operation->setMapper($dummyMapper);
        $operation->setContext(['some' => 'context']);

        $source = new PersonDto();
        $destination = new Person();

        $operation->mapProperty('name', $source, $destination);
    }
}
