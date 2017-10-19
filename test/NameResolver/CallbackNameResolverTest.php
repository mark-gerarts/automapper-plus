<?php

namespace AutoMapperPlus\NameResolver;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\MappingOperation\Operation;
use PHPUnit\Framework\TestCase;

class CallbackNameResolverTest extends TestCase
{
    public function testItResolvesNamesWithACallback()
    {
        $resolver = new CallbackNameResolver(function (string $property) {
            return strtoupper($property);
        });

        $output = $resolver->getSourcePropertyName(
            'a_property',
            Operation::ignore(),
            Options::default()
        );
        $this->assertEquals('A_PROPERTY', $output);
    }

    public function testAPropertyCanStillBeExplicitlyOverridden()
    {
        $resolver = new CallbackNameResolver(function (string $property) {
            return strtoupper($property);
        });

        $output = $resolver->getSourcePropertyName(
            'a_property',
            Operation::fromProperty('overridden'),
            Options::default()
        );
        $this->assertEquals('overridden', $output);
    }
}
