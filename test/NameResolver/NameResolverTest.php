<?php

namespace AutoMapperPlus\NameResolver;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\MappingOperation\DefaultMappingOperation;
use AutoMapperPlus\MappingOperation\Implementations\FromProperty;
use AutoMapperPlus\NameConverter\NamingConvention\CamelCaseNamingConvention;
use AutoMapperPlus\NameConverter\NamingConvention\PascalCaseNamingConvention;
use PHPUnit\Framework\TestCase;

class NameResolverTest extends TestCase
{
    /**
     * @var NameResolver
     */
    private $resolver;

    protected function setUp(): void
    {
        $this->resolver = new NameResolver();
    }

    /**
     * Without any extra configuration, a name should be resolved to its
     * identity.
     */
    public function testItResolvesANameToItsIdentity()
    {
        $operation = new DefaultMappingOperation();
        $options = Options::default();

        $output = $this->resolver->getSourcePropertyName('name', $operation, $options);
        $this->assertEquals('name', $output);
    }

    public function testItResolvesNamingConventions()
    {
        $operation = new DefaultMappingOperation();
        $options = Options::default();
        $options->setSourceMemberNamingConvention(new CamelCaseNamingConvention());
        $options->setDestinationMemberNamingConvention(new PascalCaseNamingConvention());

        $output = $this->resolver->getSourcePropertyName('PascalCase', $operation, $options);
        $this->assertEquals('pascalCase', $output);
    }

    /**
     * If the operation defines an alternate property, this should override the
     * defaults.
     */
    public function testItResolvesWithAnAlternativePropertyNameOperation()
    {
        $operation = new FromProperty('a_property');
        $options = Options::default();

        $output = $this->resolver->getSourcePropertyName('another', $operation, $options);
        $this->assertEquals('a_property', $output);

        // Let's test it with other defaults as well.
        $options->setSourceMemberNamingConvention(new CamelCaseNamingConvention());
        $options->setDestinationMemberNamingConvention(new PascalCaseNamingConvention());

        $output = $this->resolver->getSourcePropertyName('another', $operation, $options);
        $this->assertEquals('a_property', $output);
    }
}
