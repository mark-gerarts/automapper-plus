<?php

namespace AutoMapperPlus\Test\Scenarios;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use AutoMapperPlus\Test\Models\SimpleProperties\Source;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    /**
     * @group context
     */
    public function testContextCanBePassedToMap()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(Source::class, Destination::class)
            ->forMember('name', function ($source, $mapper, $context) {
                $this->assertArrayHasKey('context_key', $context);
                $this->assertEquals('context-value', $context['context_key']);

                return $context['context_key'];
            });
        $mapper = new AutoMapper($config);
        $source = new Source('a name');

        $result = $mapper->map(
            $source,
            Destination::class,
            ['context_key' => 'context-value']
        );

        $this->assertEquals('context-value', $result->name);
    }

    /**
     * @group context
     */
    public function testContextCanBePassedToMapMultiple()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(Source::class, Destination::class)
            ->forMember('name', function ($source, $mapper, $context) {
                $this->assertArrayHasKey('context_key', $context);
                $this->assertEquals('context-value', $context['context_key']);

                return $context['context_key'];
            });
        $mapper = new AutoMapper($config);
        $source = new Source('a name');

        $result = $mapper->mapMultiple(
            [$source],
            Destination::class,
            ['context_key' => 'context-value']
        );

        $this->assertEquals('context-value', $result[0]->name);
    }
}
