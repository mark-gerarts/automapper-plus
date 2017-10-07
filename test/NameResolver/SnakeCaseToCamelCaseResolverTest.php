<?php

namespace AutoMapperPlus\NameResolver;

use PHPUnit\Framework\TestCase;

/**
 * Class SnakeCaseToCamelCaseResolverTest
 *
 * @package AutoMapperPlus\NameResolver
 */
class SnakeCaseToCamelCaseResolverTest extends TestCase
{
    public function testItResolvesSnakeCaseNames()
    {
        $resolver = new SnakeCaseToCamelCaseResolver();
        $camel = 'firstName';
        $snake = 'first_name';

        $this->assertEquals($camel, $resolver->resolve($snake));
    }
}
