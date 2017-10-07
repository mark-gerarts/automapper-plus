<?php

namespace AutoMapperPlus\NameResolver;

use PHPUnit\Framework\TestCase;

/**
 * Class IdentityNameResolverTest
 *
 * @package AutoMapperPlus\NameResolver
 */
class IdentityNameResolverTest extends TestCase
{
    public function testItReturnsTheSameValue()
    {
        $resolver = new IdentityNameResolver();
        $this->assertEquals('test', $resolver->resolve('test'));
    }
}
