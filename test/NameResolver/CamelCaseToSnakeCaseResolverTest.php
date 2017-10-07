<?php

namespace AutoMapperPlus\NameResolver;

use PHPUnit\Framework\TestCase;

/**
 * Class CamelCaseToSnakeCaseResolverTest
 *
 * @package AutoMapperPlus\NameResolver
 */
class CamelCaseToSnakeCaseResolverTest extends TestCase
{
    public function testItResolvesCamelCaseNames()
    {
        $resolver = new CamelCaseToSnakeCaseResolver();
        $camel = 'firstName';
        $snake = 'first_name';

        $this->assertEquals($snake, $resolver->resolve($camel));
    }
}
